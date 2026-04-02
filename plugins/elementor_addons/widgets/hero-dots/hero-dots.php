<?php
if (!defined('ABSPATH')) exit;

class Elementor_Widget_Hero_Dots extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        wp_register_style('hero-dots-css', plugins_url('hero-dots.css', __FILE__));
    }

    public function get_style_depends() {
        return ['hero-dots-css'];
    }

    public function get_name() {
        return 'hero_dots';
    }

    public function get_title() {
        return __('Hero Dots', 'elementor_addon');
    }

    public function get_icon() {
        return 'eicon-header';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        /* ── CONTENT ── */
        $this->start_controls_section('content_section', [
            'label' => __('Contenuti', 'elementor_addon'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('top_left', [
            'label'   => __('Testo in alto – sinistra', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'IVREA (TO)',
            'label_block' => true,
        ]);

        $this->add_control('top_center', [
            'label'   => __('Testo in alto – centro', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => '19-20-21',
            'label_block' => true,
        ]);

        $this->add_control('top_right', [
            'label'   => __('Testo in alto – destra', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'GIUGNO 26',
            'label_block' => true,
        ]);

        $this->add_control('title_text', [
            'label'   => __('Titolo', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'EX MACHINA',
            'label_block' => true,
        ]);

        $this->add_control('subtitle_text', [
            'label'   => __('Sottotitolo', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => "LA COMUNITÀ CHE\nVIDE IL FUTURO",
            'label_block' => true,
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s  = $this->get_settings_for_display();
        $id = $this->get_id();
        ?>

        <div class="hero-dots-widget" id="hero-dots-<?php echo esc_attr($id); ?>">

            <!-- Canvas background -->
            <canvas class="hero-dots-canvas"></canvas>

            <!-- Content overlay -->
            <div class="hero-dots-content">

                <div class="hero-dots-top">
                    <span class="hero-dots-top-text" data-split-hover><?php echo esc_html($s['top_left']); ?></span>
                    <span class="hero-dots-top-text" data-split-hover><?php echo esc_html($s['top_center']); ?></span>
                    <span class="hero-dots-top-text" data-split-hover><?php echo esc_html($s['top_right']); ?></span>
                </div>

                <h1 class="hero-dots-title" data-split-hover><?php echo esc_html($s['title_text']); ?></h1>

                <p class="hero-dots-subtitle" data-split-hover><?php echo nl2br(esc_html($s['subtitle_text'])); ?></p>

            </div>
        </div>

        <script>
        (function(){
            "use strict";

            /* ── Dot-grid background with cursor repulsion ── */
            function initHeroDots(wrapper) {
                var canvas = wrapper.querySelector(".hero-dots-canvas");
                if (!canvas) return;
                var ctx = canvas.getContext("2d");

                var DOT_BASE   = 3;          // base radius px
                var GAP        = 10;          // gap between dots
                var STEP       = DOT_BASE * 2 + GAP; // center-to-center
                var INFLUENCE  = 70;         // cursor influence radius
                var MAX_SCALE  = 2.8;        // max scale multiplier when hovered
                var BG_COLOR   = "#FF3333";
                var DOT_COLOR  = "#E02D2D";  // slightly darker dots

                var cols, rows, dots;
                var mouseX = -9999, mouseY = -9999;
                var raf;
                var dpr = window.devicePixelRatio || 1;

                function resize() {
                    var rect = wrapper.getBoundingClientRect();
                    dpr = window.devicePixelRatio || 1;
                    canvas.width  = rect.width * dpr;
                    canvas.height = rect.height * dpr;
                    canvas.style.width  = rect.width + "px";
                    canvas.style.height = rect.height + "px";
                    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                    buildGrid();
                }

                function buildGrid() {
                    var w = canvas.width / dpr;
                    var h = canvas.height / dpr;
                    cols = Math.ceil(w / STEP) + 1;
                    rows = Math.ceil(h / STEP) + 1;
                    dots = new Float32Array(cols * rows);
                    for (var i = 0; i < dots.length; i++) dots[i] = 1;
                }

                function draw() {
                    var w = canvas.width / dpr;
                    var h = canvas.height / dpr;
                    ctx.fillStyle = BG_COLOR;
                    ctx.fillRect(0, 0, w, h);

                    ctx.fillStyle = DOT_COLOR;

                    for (var r = 0; r < rows; r++) {
                        for (var c = 0; c < cols; c++) {
                            var idx = r * cols + c;
                            var cx  = c * STEP + DOT_BASE;
                            var cy  = r * STEP + DOT_BASE;

                            var dx = cx - mouseX;
                            var dy = cy - mouseY;
                            var dist = Math.sqrt(dx * dx + dy * dy);

                            var targetScale = 1;
                            if (dist < INFLUENCE) {
                                targetScale = 1 + (MAX_SCALE - 1) * (1 - dist / INFLUENCE);
                            }

                            // lerp for smooth transition
                            dots[idx] += (targetScale - dots[idx]) * 0.15;

                            var radius = DOT_BASE * dots[idx];

                            ctx.beginPath();
                            ctx.arc(cx, cy, radius, 0, Math.PI * 2);
                            ctx.fill();
                        }
                    }

                    raf = requestAnimationFrame(draw);
                }

                function onMouseMove(e) {
                    var rect = canvas.getBoundingClientRect();
                    mouseX = e.clientX - rect.left;
                    mouseY = e.clientY - rect.top;
                }

                function onMouseLeave() {
                    mouseX = -9999;
                    mouseY = -9999;
                }

                wrapper.addEventListener("mousemove", onMouseMove);
                wrapper.addEventListener("mouseleave", onMouseLeave);

                window.addEventListener("resize", resize);
                resize();
                draw();

                // cleanup on widget removal (Elementor editor)
                wrapper._heroDotsCleanup = function() {
                    cancelAnimationFrame(raf);
                    window.removeEventListener("resize", resize);
                    wrapper.removeEventListener("mousemove", onMouseMove);
                    wrapper.removeEventListener("mouseleave", onMouseLeave);
                };
            }

            /* ── Variable font hover effect (same as hero-text) ── */
            var TEXT_RADIUS = 300;
            var TEXT_MIN_WEIGHT = 80;
            var TEXT_MAX_WEIGHT = 200;
            var TEXT_LERP = 0.08;
            var textMouseX = -9999, textMouseY = -9999;

            function lerpVal(a, b, t) { return a + (b - a) * t; }

            function splitTextIntoChars(element) {
                var html = element.innerHTML;
                var lines = html.split(/<br\s*\/?>/i);
                element.innerHTML = "";
                element.setAttribute("aria-label", element.textContent);

                for (var l = 0; l < lines.length; l++) {
                    var lineText = lines[l].replace(/<[^>]*>/g, "").trim();
                    var words = lineText.split(" ");

                    for (var w = 0; w < words.length; w++) {
                        if (words[w] === "") continue;
                        var wordSpan = document.createElement("span");
                        wordSpan.classList.add("word");

                        for (var i = 0; i < words[w].length; i++) {
                            var span = document.createElement("span");
                            span.classList.add("char");
                            span.setAttribute("aria-hidden", "true");
                            span.textContent = words[w][i];
                            span._currentWeight = TEXT_MIN_WEIGHT;
                            span._targetWeight = TEXT_MIN_WEIGHT;
                            wordSpan.appendChild(span);
                        }

                        element.appendChild(wordSpan);

                        if (w < words.length - 1) {
                            var space = document.createElement("span");
                            space.classList.add("char", "space");
                            space.innerHTML = "&nbsp;";
                            space._currentWeight = TEXT_MIN_WEIGHT;
                            space._targetWeight = TEXT_MIN_WEIGHT;
                            element.appendChild(space);
                        }
                    }

                    if (l < lines.length - 1) {
                        element.appendChild(document.createElement("br"));
                    }
                }
            }

            function initTextHover(wrapper) {
                var elements = wrapper.querySelectorAll("[data-split-hover]");
                elements.forEach(function(el) { splitTextIntoChars(el); });

                var allChars = wrapper.querySelectorAll(".char");
                var isHovering = false;
                var animId = null;

                function animate() {
                    var needsUpdate = false;
                    allChars.forEach(function(ch) {
                        if (isHovering) {
                            var r = ch.getBoundingClientRect();
                            var cx = r.left + r.width / 2;
                            var cy = r.top + r.height / 2;
                            var dx = textMouseX - cx;
                            var dy = textMouseY - cy;
                            var dist = Math.sqrt(dx * dx + dy * dy);
                            if (dist < TEXT_RADIUS) {
                                var ratio = 1 - dist / TEXT_RADIUS;
                                ratio = ratio * ratio;
                                ch._targetWeight = TEXT_MIN_WEIGHT + ratio * (TEXT_MAX_WEIGHT - TEXT_MIN_WEIGHT);
                            } else {
                                ch._targetWeight = TEXT_MIN_WEIGHT;
                            }
                        } else {
                            ch._targetWeight = TEXT_MIN_WEIGHT;
                        }
                        ch._currentWeight = lerpVal(ch._currentWeight, ch._targetWeight, TEXT_LERP);
                        if (Math.abs(ch._currentWeight - ch._targetWeight) > 0.5) needsUpdate = true;
                        ch.style.fontWeight = Math.round(ch._currentWeight);
                    });
                    if (needsUpdate || isHovering) animId = requestAnimationFrame(animate);
                    else animId = null;
                }

                function startAnim() { if (!animId) animId = requestAnimationFrame(animate); }

                wrapper.addEventListener("mousemove", function(e) {
                    textMouseX = e.clientX;
                    textMouseY = e.clientY;
                    isHovering = true;
                    startAnim();
                });
                wrapper.addEventListener("mouseleave", function() {
                    isHovering = false;
                    startAnim();
                });
            }

            function init() {
                var el = document.getElementById("hero-dots-<?php echo esc_attr($id); ?>");
                if (el) {
                    if (el._heroDotsCleanup) el._heroDotsCleanup();
                    initHeroDots(el);
                    initTextHover(el);
                }
            }

            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", init);
            } else {
                init();
            }

            if (window.elementorFrontend) {
                window.elementorFrontend.hooks.addAction(
                    "frontend/element_ready/hero_dots.default",
                    function($scope) {
                        var el = $scope[0].querySelector(".hero-dots-widget");
                        if (el) {
                            if (el._heroDotsCleanup) el._heroDotsCleanup();
                            initHeroDots(el);
                            initTextHover(el);
                        }
                    }
                );
            }
        })();
        </script>

        <?php
    }
}
