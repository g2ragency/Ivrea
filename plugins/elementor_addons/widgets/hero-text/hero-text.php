<?php
if (!defined('ABSPATH')) exit;

class Elementor_Widget_Hero_Text extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style('hero-text-css', plugins_url('hero-text.css', __FILE__));
    }

    public function get_style_depends() {
        return ['hero-text-css'];
    }

    public function get_name() {
        return 'hero_text';
    }

    public function get_title() {
        return __('Hero Text', 'elementor_addon');
    }

    public function get_icon() {
        return 'eicon-t-letter-bold';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Contenuti', 'elementor_addon'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title_text',
            [
                'label' => __('Titolo', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => "IVREA\nEX MACHINA",
                'label_block' => true,
            ]
        );

        $this->add_control(
            'dates_text',
            [
                'label' => __('Date', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => "19-20-21\nGIUGNO '26",
                'label_block' => true,
            ]
        );

        $this->add_control(
            'subtitle_text',
            [
                'label' => __('Sottotitolo', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'LA CITTÀ CHE VIDE IL FUTURO',
                'label_block' => true,
            ]
        );

        $this->end_controls_section();

        // Style section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Stile', 'elementor_addon'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Colore Titolo', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f0f0f0',
            ]
        );

        $this->add_control(
            'dates_color',
            [
                'label' => __('Colore Date', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );

        $this->add_control(
            'subtitle_color',
            [
                'label' => __('Colore Sottotitolo', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $title = $settings['title_text'];
        $dates = $settings['dates_text'];
        $subtitle = $settings['subtitle_text'];
        $title_color = $settings['title_color'];
        $dates_color = $settings['dates_color'];
        $subtitle_color = $settings['subtitle_color'];
        ?>

        <div class="hero-text-widget" data-hover-effect="true">
            <div class="hero-dates" style="color: <?php echo esc_attr($dates_color); ?>;" data-split-hover>
                <?php echo nl2br(esc_html($dates)); ?>
            </div>
            <h1 class="hero-title" style="color: <?php echo esc_attr($title_color); ?>;" data-split-hover>
                <?php echo nl2br(esc_html($title)); ?>
            </h1>
            <div class="hero-subtitle" style="color: <?php echo esc_attr($subtitle_color); ?>;" data-split-hover>
                <?php echo esc_html($subtitle); ?>
            </div>
        </div>

        <script>
        (function () {
            "use strict";

            var RADIUS = 300;
            var MIN_WEIGHT = 80;
            var MAX_WEIGHT = 200;
            var LERP_SPEED = 0.08;

            var mouseX = -9999;
            var mouseY = -9999;

            /* ── Gyroscope state ── */
            var isTouchDevice = "ontouchstart" in window || navigator.maxTouchPoints > 0;
            var tiltGamma = 0, tiltBeta = 0, tiltActive = false;
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

            if (isTouchDevice) {
                window.addEventListener("deviceorientation", onDeviceOrientation);
            }

            function lerp(start, end, factor) {
                return start + (end - start) * factor;
            }

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
                            span._currentWeight = MIN_WEIGHT;
                            span._targetWeight = MIN_WEIGHT;
                            wordSpan.appendChild(span);
                        }

                        element.appendChild(wordSpan);

                        if (w < words.length - 1) {
                            var space = document.createElement("span");
                            space.classList.add("char", "space");
                            space.innerHTML = "&nbsp;";
                            space._currentWeight = MIN_WEIGHT;
                            space._targetWeight = MIN_WEIGHT;
                            element.appendChild(space);
                        }
                    }

                    if (l < lines.length - 1) {
                        element.appendChild(document.createElement("br"));
                    }
                }
            }

            function initHoverEffect(widget) {
                var elements = widget.querySelectorAll("[data-split-hover]");
                elements.forEach(function (el) {
                    splitTextIntoChars(el);
                });

                var allChars = widget.querySelectorAll(".char");
                var isHovering = false;
                var animationId = null;

                function animate() {
                    var needsUpdate = false;

                    allChars.forEach(function (charEl) {
                        if (tiltActive && isTouchDevice) {
                            var charRect = charEl.getBoundingClientRect();
                            var normX = (charRect.left + charRect.width / 2) / window.innerWidth * 2 - 1;
                            var normY = (charRect.top + charRect.height / 2) / window.innerHeight * 2 - 1;
                            var influence = tiltGamma * normX + tiltBeta * normY;
                            influence = Math.max(0, Math.min(1, influence));
                            charEl._targetWeight = MIN_WEIGHT + influence * (MAX_WEIGHT - MIN_WEIGHT);
                            needsUpdate = true;
                        } else if (isHovering) {
                            var charRect = charEl.getBoundingClientRect();
                            var charCenterX = charRect.left + charRect.width / 2;
                            var charCenterY = charRect.top + charRect.height / 2;

                            var dx = mouseX - charCenterX;
                            var dy = mouseY - charCenterY;
                            var distance = Math.sqrt(dx * dx + dy * dy);

                            if (distance < RADIUS) {
                                var ratio = 1 - (distance / RADIUS);
                                ratio = ratio * ratio;
                                charEl._targetWeight = MIN_WEIGHT + ratio * (MAX_WEIGHT - MIN_WEIGHT);
                            } else {
                                charEl._targetWeight = MIN_WEIGHT;
                            }
                        } else {
                            charEl._targetWeight = MIN_WEIGHT;
                        }

                        charEl._currentWeight = lerp(charEl._currentWeight, charEl._targetWeight, LERP_SPEED);

                        if (Math.abs(charEl._currentWeight - charEl._targetWeight) > 0.5) {
                            needsUpdate = true;
                        }

                        charEl.style.fontWeight = Math.round(charEl._currentWeight);
                    });

                    if (needsUpdate || isHovering || tiltActive) {
                        animationId = requestAnimationFrame(animate);
                    } else {
                        animationId = null;
                    }
                }

                function startAnimation() {
                    if (!animationId) {
                        animationId = requestAnimationFrame(animate);
                    }
                }

                if (!isTouchDevice) {
                    widget.addEventListener("mousemove", function (e) {
                        mouseX = e.clientX;
                        mouseY = e.clientY;
                        isHovering = true;
                        startAnimation();
                    });

                    widget.addEventListener("mouseleave", function () {
                        isHovering = false;
                        startAnimation();
                    });
                }

                if (isTouchDevice) { startAnimation(); }
            }

            function init() {
                var widgets = document.querySelectorAll('.hero-text-widget[data-hover-effect="true"]');
                widgets.forEach(function (widget) {
                    initHoverEffect(widget);
                });
            }

            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", init);
            } else {
                init();
            }

            if (window.elementorFrontend) {
                window.elementorFrontend.hooks.addAction(
                    "frontend/element_ready/hero_text.default",
                    function ($scope) {
                        var widget = $scope[0].querySelector('.hero-text-widget[data-hover-effect="true"]');
                        if (widget) {
                            initHoverEffect(widget);
                        }
                    }
                );
            }
        })();
        </script>

        <?php
    }

    protected function content_template() {}
}
