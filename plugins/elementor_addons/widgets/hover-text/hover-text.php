<?php
if (!defined('ABSPATH')) exit;

class Elementor_Widget_Hover_Text extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style('hover-text-css', plugins_url('hover-text.css', __FILE__));
    }

    public function get_style_depends() {
        return ['hover-text-css'];
    }

    public function get_name() {
        return 'hover_text';
    }

    public function get_title() {
        return __('Hover Text', 'elementor_addon');
    }

    public function get_icon() {
        return 'eicon-animation-text';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Contenuto', 'elementor_addon'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'hover_text',
            [
                'label' => __('Testo', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'HOVER TEXT',
                'label_block' => true,
                'description' => __('Usa Invio per andare a capo.', 'elementor_addon'),
            ]
        );

        $this->add_control(
            'hover_link',
            [
                'label' => __('Link', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => 'https://example.com',
                'default' => [
                    'url' => '',
                    'is_external' => false,
                    'nofollow' => false,
                ],
                'label_block' => true,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Stile', 'elementor_addon'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Colore Testo', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .hover-text-content' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .hover-text-content .char' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .hover-text-content .word' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'font_size',
            [
                'label' => __('Font Size', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 12,
                        'max' => 400,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0.5,
                        'max' => 20,
                        'step' => 0.1,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 30,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 80,
                ],
                'selectors' => [
                    '{{WRAPPER}} .hover-text-content' => 'font-size: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_wrap',
            [
                'label' => __('Testo a capo', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sì', 'elementor_addon'),
                'label_off' => __('No', 'elementor_addon'),
                'return_value' => 'yes',
                'default' => '',
                'selectors_dictionary' => [
                    'yes' => 'white-space: normal;',
                    '' => 'white-space: nowrap;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .hover-text-widget .word' => '{{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'font_weight',
            [
                'label' => __('Font Weight', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 80,
                'min' => 1,
                'max' => 900,
                'step' => 10,
                'description' => __('Peso base del font (min). Al hover arriverà a +120.', 'elementor_addon'),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $text = $settings['hover_text'];
        $font_weight = $settings['font_weight'];
        $link = $settings['hover_link'];
        $widget_id = $this->get_id();

        $has_link = !empty($link['url']);
        $target = (!empty($link['is_external'])) ? ' target="_blank"' : '';
        $nofollow = (!empty($link['nofollow'])) ? ' rel="nofollow"' : '';
        $tag = $has_link ? 'a' : 'h3';
        $link_attr = $has_link ? ' href="' . esc_url($link['url']) . '"' . $target . $nofollow : '';
        ?>

        <div class="hover-text-widget" id="hover-text-<?php echo esc_attr($widget_id); ?>" data-hover-effect="true" data-font-weight="<?php echo esc_attr($font_weight); ?>">
            <<?php echo $tag; ?> class="hover-text-content"<?php echo $link_attr; ?> style="font-weight: <?php echo esc_attr($font_weight); ?>;" data-split-hover>
                <?php echo nl2br(esc_html($text)); ?>
            </<?php echo $tag; ?>>
        </div>

        <script>
        (function () {
            "use strict";

            var RADIUS = 300;
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

            function splitTextIntoChars(element, MIN_WEIGHT) {
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
                var MIN_WEIGHT = parseInt(widget.getAttribute("data-font-weight")) || 80;
                var MAX_WEIGHT = MIN_WEIGHT + 160;

                var elements = widget.querySelectorAll("[data-split-hover]");
                elements.forEach(function (el) {
                    splitTextIntoChars(el, MIN_WEIGHT);
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
                var widget = document.getElementById("hover-text-<?php echo esc_attr($widget_id); ?>");
                if (widget) {
                    initHoverEffect(widget);
                }
            }

            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", init);
            } else {
                init();
            }

            if (window.elementorFrontend) {
                window.elementorFrontend.hooks.addAction(
                    "frontend/element_ready/hover_text.default",
                    function ($scope) {
                        var widget = $scope[0].querySelector('.hover-text-widget[data-hover-effect="true"]');
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
