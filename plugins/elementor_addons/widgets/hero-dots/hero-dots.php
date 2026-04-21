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

        $this->add_responsive_control('vertical_alignment', [
            'label' => __('Allineamento Verticale', 'elementor_addon'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [ 'title' => 'Alto', 'icon' => 'eicon-v-align-top' ],
                'center'     => [ 'title' => 'Centro', 'icon' => 'eicon-v-align-middle' ],
                'flex-end'   => [ 'title' => 'Basso', 'icon' => 'eicon-v-align-bottom' ],
            ],
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .hero-dots-content' => 'justify-content: {{VALUE}};',
            ],
        ]);

        $this->add_control('top_left', [
            'label'   => __('Testo alto-sinistra (Desktop)', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => 'IVREA (TO)',
        ]);
        $this->add_control('top_left_mobile', [
            'label'   => __('Testo alto-sinistra (Mobile)', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
        ]);

        $this->add_control('top_center', [
            'label'   => __('Testo alto-centro (Desktop)', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => '19-20-21',
        ]);
        $this->add_control('top_center_mobile', [
            'label'   => __('Testo alto-centro (Mobile)', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
        ]);

        $this->add_control('top_right', [
            'label'   => __('Testo alto-destra (Desktop)', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => 'GIUGNO 26',
        ]);
        $this->add_control('top_right_mobile', [
            'label'   => __('Testo alto-destra (Mobile)', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
        ]);

        $this->add_control('title_text', [
            'label'   => __('Titolo (Desktop)', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => 'EX MACHINA',
        ]);
        $this->add_control('title_text_mobile', [
            'label'   => __('Titolo (Mobile)', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
        ]);

        $this->add_control('subtitle_text', [
            'label'   => __('Sottotitolo (Desktop)', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => "LA COMUNITÀ CHE\nVIDE IL FUTURO",
        ]);
        $this->add_control('subtitle_text_mobile', [
            'label'   => __('Sottotitolo (Mobile)', 'elementor_addon'),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
        ]);

        $this->end_controls_section();

        /* ── STILE ── */
        $this->start_controls_section('style_section', [
            'label' => __('Stile', 'elementor_addon'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('widget_height', [
            'label'       => __('Altezza Widget', 'elementor_addon'),
            'type'        => \Elementor\Controls_Manager::SLIDER,
            'size_units'  => ['px', 'vh', 'rem'],
            'range'       => [
                'px' => ['min' => 200, 'max' => 1500],
                'vh' => ['min' => 10, 'max' => 100],
            ],
            'selectors'   => [
                '{{WRAPPER}} .hero-dots-widget' => 'height: {{SIZE}}{{UNIT}};',
            ],
            'default' => [
                'unit' => 'vh',
                'size' => 100,
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s  = $this->get_settings_for_display();
        $id = $this->get_id();
        
        // Fallback for mobile missing versions
        $top_left_m   = !empty($s['top_left_mobile']) ? $s['top_left_mobile'] : $s['top_left'];
        $top_center_m = !empty($s['top_center_mobile']) ? $s['top_center_mobile'] : $s['top_center'];
        $top_right_m  = !empty($s['top_right_mobile']) ? $s['top_right_mobile'] : $s['top_right'];
        $title_m      = !empty($s['title_text_mobile']) ? $s['title_text_mobile'] : $s['title_text'];
        $subtitle_m   = !empty($s['subtitle_text_mobile']) ? $s['subtitle_text_mobile'] : $s['subtitle_text'];
        ?>

        <div class="hero-dots-widget" id="hero-dots-<?php echo esc_attr($id); ?>">

            <!-- Canvas background -->
            <canvas class="hero-dots-canvas"></canvas>

            <!-- iOS gyro permission CTA (hidden by default) -->
            <button class="hero-dots-gyro-cta" style="display:none;" aria-label="Attiva effetto inclinazione">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12" y2="18"/></svg>
                <span>Tocca per attivare l'effetto</span>
            </button>

            <!-- Content overlay -->
            <div class="hero-dots-content">

                <!-- Desktop Content -->
                <div class="hero-dots-inner-content d-version-desktop">
                    <div class="hero-dots-top">
                        <span class="hero-dots-top-text" data-split-hover><?php echo nl2br(esc_html($s['top_left'])); ?></span>
                        <span class="hero-dots-top-text" data-split-hover><?php echo nl2br(esc_html($s['top_center'])); ?></span>
                        <span class="hero-dots-top-text" data-split-hover><?php echo nl2br(esc_html($s['top_right'])); ?></span>
                    </div>

                    <h1 class="hero-dots-title" data-split-hover><?php echo nl2br(esc_html($s['title_text'])); ?></h1>
                    <p class="hero-dots-subtitle" data-split-hover><?php echo nl2br(esc_html($s['subtitle_text'])); ?></p>
                </div>

                <!-- Mobile Content -->
                <div class="hero-dots-inner-content d-version-mobile">
                    <div class="hero-dots-top">
                        <span class="hero-dots-top-text" data-split-hover><?php echo nl2br(esc_html($top_left_m)); ?></span>
                        <span class="hero-dots-top-text" data-split-hover><?php echo nl2br(esc_html($top_center_m)); ?></span>
                        <span class="hero-dots-top-text" data-split-hover><?php echo nl2br(esc_html($top_right_m)); ?></span>
                    </div>

                    <h1 class="hero-dots-title" data-split-hover><?php echo nl2br(esc_html($title_m)); ?></h1>
                    <p class="hero-dots-subtitle" data-split-hover><?php echo nl2br(esc_html($subtitle_m)); ?></p>
                </div>

            </div>
        </div>

        <script>
        (function(){
            "use strict";

            /* ── Shared gyroscope state (used by dots + text) ── */
            var isTouchDevice = "ontouchstart" in window || navigator.maxTouchPoints > 0;
            var tiltGamma = 0;
            var tiltBeta  = 0;
            var tiltActive = false;
            var TILT_DEADZONE = 3;

            function onDeviceOrientation(e) {
                if (e.gamma === null && e.beta === null) return;
                tiltActive = true;
                var g = e.gamma || 0;
                var b = (e.beta || 0) - 45;
                if (Math.abs(g) < TILT_DEADZONE) g = 0;
                if (Math.abs(b) < TILT_DEADZONE) b = 0;
                tiltGamma = Math.max(-1, Math.min(1, g / 35));
                tiltBeta  = Math.max(-1, Math.min(1, b / 35));
            }

            /* ── Dot-grid background with cursor repulsion + gyroscope tilt ── */
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
                var TILT_MAX_SCALE = 2.8;

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

                            var targetScale = 1;

                            if (tiltActive) {
                                /* ── Gyroscope tilt mode (mobile/tablet) ── */
                                var normX = w > 0 ? (cx / w - 0.5) * 2 : 0;  // -1 (left) to 1 (right)
                                var normY = h > 0 ? (cy / h - 0.5) * 2 : 0;  // -1 (top) to 1 (bottom)

                                // Dot product: dots aligned with tilt direction grow
                                var influence = tiltGamma * normX + tiltBeta * normY;
                                // Clamp to [0, 1] — only grow, never shrink below base
                                influence = Math.max(0, Math.min(1, influence));
                                targetScale = 1 + influence * (TILT_MAX_SCALE - 1);
                            } else {
                                /* ── Desktop cursor repulsion ── */
                                var dx = cx - mouseX;
                                var dy = cy - mouseY;
                                var dist = Math.sqrt(dx * dx + dy * dy);

                                if (dist < INFLUENCE) {
                                    targetScale = 1 + (MAX_SCALE - 1) * (1 - dist / INFLUENCE);
                                }
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

                /* ── Desktop mouse handlers ── */
                function onMouseMove(e) {
                    var rect = canvas.getBoundingClientRect();
                    mouseX = e.clientX - rect.left;
                    mouseY = e.clientY - rect.top;
                }

                function onMouseLeave() {
                    mouseX = -9999;
                    mouseY = -9999;
                }

                /* ── Gyroscope handler is shared (outer scope) ── */

                /* ── iOS CTA helpers ── */
                var ctaBtn = wrapper.querySelector(".hero-dots-gyro-cta");

                function showCta() {
                    if (ctaBtn) { ctaBtn.style.display = "flex"; }
                }

                function hideCta() {
                    if (ctaBtn) {
                        ctaBtn.style.opacity = "0";
                        setTimeout(function() { ctaBtn.style.display = "none"; }, 400);
                    }
                }

                /* ── iOS permission flow ── */
                function requestGyroPermission() {
                    if (typeof DeviceOrientationEvent !== "undefined" &&
                        typeof DeviceOrientationEvent.requestPermission === "function") {
                        // iOS 13+ requires user gesture
                        DeviceOrientationEvent.requestPermission().then(function(state) {
                            hideCta();
                            if (state === "granted") {
                                window.addEventListener("deviceorientation", onDeviceOrientation);
                            }
                        }).catch(function() {
                            hideCta();
                        });
                    } else {
                        // Android / other — no permission needed
                        window.addEventListener("deviceorientation", onDeviceOrientation);
                    }
                }

                /* ── Bind events based on device type ── */
                if (isTouchDevice) {
                    var needsPermission = typeof DeviceOrientationEvent !== "undefined" &&
                        typeof DeviceOrientationEvent.requestPermission === "function";

                    if (needsPermission) {
                        // iOS: show CTA button, bind click to it
                        showCta();
                        ctaBtn.addEventListener("click", function() {
                            requestGyroPermission();
                        }, { once: true });
                    } else {
                        // Android: start gyro immediately, no permission needed
                        requestGyroPermission();
                    }
                    // Do NOT bind mouse events on touch — iOS emulates fake mousemove
                } else {
                    // Desktop only: bind mouse events
                    wrapper.addEventListener("mousemove", onMouseMove);
                    wrapper.addEventListener("mouseleave", onMouseLeave);
                }

                window.addEventListener("resize", resize);
                resize();
                draw();

                // cleanup on widget removal (Elementor editor)
                wrapper._heroDotsCleanup = function() {
                    cancelAnimationFrame(raf);
                    window.removeEventListener("resize", resize);
                    window.removeEventListener("deviceorientation", onDeviceOrientation);
                    if (!isTouchDevice) {
                        wrapper.removeEventListener("mousemove", onMouseMove);
                        wrapper.removeEventListener("mouseleave", onMouseLeave);
                    }
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
                            element.appendChild(document.createTextNode(" "));
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
                        var r = ch.getBoundingClientRect();
                        if(r.width === 0 && r.height === 0) {
                            ch._targetWeight = TEXT_MIN_WEIGHT;
                            ch._currentWeight = TEXT_MIN_WEIGHT;
                            return;
                        }
                        if (tiltActive && isTouchDevice) {
                            /* ── Gyroscope mode: weight based on char position ── */
                            var r = ch.getBoundingClientRect();
                            var normX = (r.left + r.width / 2) / window.innerWidth * 2 - 1;
                            var normY = (r.top + r.height / 2) / window.innerHeight * 2 - 1;
                            var influence = tiltGamma * normX + tiltBeta * normY;
                            influence = Math.max(0, Math.min(1, influence));
                            ch._targetWeight = TEXT_MIN_WEIGHT + influence * (TEXT_MAX_WEIGHT - TEXT_MIN_WEIGHT);
                            needsUpdate = true;
                        } else if (isHovering) {
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
                    if (needsUpdate || isHovering || tiltActive) animId = requestAnimationFrame(animate);
                    else animId = null;
                }

                function startAnim() { if (!animId) animId = requestAnimationFrame(animate); }

                if (!isTouchDevice) {
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

                // On touch devices, start animation loop to react to tilt
                if (isTouchDevice) { startAnim(); }
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
