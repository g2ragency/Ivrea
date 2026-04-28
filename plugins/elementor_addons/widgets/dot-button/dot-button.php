<?php
if (!defined('ABSPATH')) exit;

class Elementor_Widget_Dot_Button extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        wp_register_style('dot-button-css', plugins_url('dot-button.css', __FILE__));
    }

    public function get_style_depends() {
        return ['dot-button-css'];
    }

    public function get_name() {
        return 'dot_button';
    }

    public function get_title() {
        return __('Dot Button', 'elementor_addon');
    }

    public function get_icon() {
        return 'eicon-button';
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
            'button_text',
            [
                'label' => __('Testo', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'SCOPRI IL PROGRAMMA',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'button_link',
            [
                'label' => __('Link', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => 'https://example.com',
                'default' => [
                    'url' => '#',
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
                'default' => '#ff3333',
            ]
        );

        $this->add_control(
            'arrow_color',
            [
                'label' => __('Colore Freccia', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );

        $this->add_control(
            'border_color',
            [
                'label' => __('Colore Bordo (dots)', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#737373',
            ]
        );

        $this->add_responsive_control(
            'font_size',
            [
                'label' => __('Font Size testo', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vw'],
                'range' => [
                    'px' => ['min' => 12, 'max' => 300, 'step' => 1],
                    'vw' => ['min' => 1, 'max' => 20, 'step' => 0.1],
                ],
                'default' => ['unit' => 'px', 'size' => 40],
                'selectors' => [
                    '{{WRAPPER}} .dot-button-text' => 'font-size: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_size',
            [
                'label' => __('Font Size freccia', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vw'],
                'range' => [
                    'px' => ['min' => 12, 'max' => 300, 'step' => 1],
                    'vw' => ['min' => 1, 'max' => 20, 'step' => 0.1],
                ],
                'default' => ['unit' => 'px', 'size' => 60],
                'selectors' => [
                    '{{WRAPPER}} .dot-button-arrow' => 'font-size: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'dot_size',
            [
                'label' => __('Font Size dots bordo (px)', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 28,
                'min' => 8,
                'max' => 80,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();

        $text = $settings['button_text'];
        $link = $settings['button_link'];
        $text_color = $settings['text_color'];
        $arrow_color = $settings['arrow_color'];
        $border_color = $settings['border_color'];
        $dot_size = $settings['dot_size'];

        $has_link = !empty($link['url']);
        $target = !empty($link['is_external']) ? ' target="_blank"' : '';
        $rel_values = [];
        if (!empty($link['nofollow'])) $rel_values[] = 'nofollow';
        if (!empty($link['is_external'])) $rel_values[] = 'noopener';
        $rel_attr = !empty($rel_values) ? ' rel="' . esc_attr(implode(' ', $rel_values)) . '"' : '';

        $tag = $has_link ? 'a' : 'div';
        $link_attr = $has_link ? ' href="' . esc_url($link['url']) . '"' . $target . $rel_attr : '';
        ?>

        <div class="dot-button-widget" id="dot-button-<?php echo esc_attr($widget_id); ?>" data-dot-size="<?php echo esc_attr($dot_size); ?>" data-border-color="<?php echo esc_attr($border_color); ?>">
            <<?php echo $tag; ?> class="dot-button-link"<?php echo $link_attr; ?>>
                <span class="dot-button-dots" aria-hidden="true"></span>

                <span class="dot-button-inner">
                    <span class="dot-button-text" style="color: <?php echo esc_attr($text_color); ?>;" data-split-hover><?php echo esc_html($text); ?></span>
                    <span class="dot-button-arrow" style="color: <?php echo esc_attr($arrow_color); ?>;" data-split-hover>→</span>
                </span>
            </<?php echo $tag; ?>>
        </div>

        <script>
        (function () {
            "use strict";

            var RADIUS = 300;
            var LERP_SPEED = 0.08;
            var MIN_WEIGHT = 140;
            var MAX_WEIGHT = 240;
            var BORDER_RADIUS = 50;

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

            function lerp(a, b, t) { return a + (b - a) * t; }

            function splitIntoChars(el) {
                var text = el.textContent.trim();
                el.innerHTML = '';
                var words = text.split(/\s+/);
                for (var w = 0; w < words.length; w++) {
                    var wordSpan = document.createElement('span');
                    wordSpan.classList.add('word');
                    for (var i = 0; i < words[w].length; i++) {
                        var span = document.createElement('span');
                        span.classList.add('char');
                        span.textContent = words[w][i];
                        span._currentWeight = MIN_WEIGHT;
                        span._targetWeight = MIN_WEIGHT;
                        wordSpan.appendChild(span);
                    }
                    el.appendChild(wordSpan);
                    if (w < words.length - 1) {
                        el.appendChild(document.createTextNode(" "));
                    }
                }
            }

            /* Generate points along a rounded rectangle path */
            function getRoundedRectPoints(w, h, r, spacing) {
                var points = [];
                r = Math.min(r, w / 2, h / 2);

                /* Straight segments lengths */
                var topLen = w - 2 * r;
                var rightLen = h - 2 * r;
                var bottomLen = w - 2 * r;
                var leftLen = h - 2 * r;
                /* Corner arc length (quarter circle) */
                var cornerLen = Math.PI * r / 2;
                var totalLen = topLen + rightLen + bottomLen + leftLen + 4 * cornerLen;

                var count = Math.max(4, Math.round(totalLen / spacing));
                var step = totalLen / count;

                for (var i = 0; i < count; i++) {
                    var d = i * step;
                    var x, y;

                    /* Top edge: left to right */
                    if (d < topLen) {
                        x = r + d;
                        y = 0;
                    }
                    /* Top-right corner */
                    else if (d < topLen + cornerLen) {
                        var a = (d - topLen) / r;
                        x = w - r + Math.sin(a) * r;
                        y = r - Math.cos(a) * r;
                    }
                    /* Right edge: top to bottom */
                    else if (d < topLen + cornerLen + rightLen) {
                        x = w;
                        y = r + (d - topLen - cornerLen);
                    }
                    /* Bottom-right corner */
                    else if (d < topLen + 2 * cornerLen + rightLen) {
                        var a = (d - topLen - cornerLen - rightLen) / r;
                        x = w - r + Math.cos(a) * r;
                        y = h - r + Math.sin(a) * r;
                    }
                    /* Bottom edge: right to left */
                    else if (d < 2 * topLen + 2 * cornerLen + rightLen) {
                        x = w - r - (d - topLen - 2 * cornerLen - rightLen);
                        y = h;
                    }
                    /* Bottom-left corner */
                    else if (d < 2 * topLen + 3 * cornerLen + rightLen) {
                        var a = (d - 2 * topLen - 2 * cornerLen - rightLen) / r;
                        x = r - Math.sin(a) * r;
                        y = h - r + Math.cos(a) * r;
                    }
                    /* Left edge: bottom to top */
                    else if (d < 2 * topLen + 3 * cornerLen + 2 * leftLen) {
                        x = 0;
                        y = h - r - (d - 2 * topLen - 3 * cornerLen - rightLen);
                    }
                    /* Top-left corner */
                    else {
                        var a = (d - 2 * topLen - 3 * cornerLen - rightLen - leftLen) / r;
                        x = r - Math.cos(a) * r;
                        y = r - Math.sin(a) * r;
                    }

                    points.push({ x: x, y: y });
                }

                return points;
            }

            function placeDots(widget) {
                var dotsContainer = widget.querySelector('.dot-button-dots');
                var linkEl = widget.querySelector('.dot-button-link');
                var dotSize = parseFloat(widget.getAttribute('data-dot-size')) || 28;
                var borderColor = widget.getAttribute('data-border-color') || '#737373';

                var rect = linkEl.getBoundingClientRect();
                var w = rect.width;
                var h = rect.height;

                /* Spacing between dot centers */
                var spacing = dotSize * 0.55;
                var points = getRoundedRectPoints(w, h, BORDER_RADIUS, spacing);

                dotsContainer.innerHTML = '';
                var offsetX = dotSize * 0.3;
                var offsetY = dotSize * 0.65; /* '.' sits at the bottom of the em-box in TINY5x3 */

                for (var i = 0; i < points.length; i++) {
                    var span = document.createElement('span');
                    span.classList.add('char', 'dot-char');
                    span.textContent = '.';
                    span._currentWeight = MIN_WEIGHT;
                    span._targetWeight = MIN_WEIGHT;
                    span.style.left = (points[i].x - offsetX) + 'px';
                    span.style.top = (points[i].y - offsetY) + 'px';
                    span.style.fontSize = dotSize + 'px';
                    span.style.color = borderColor;
                    dotsContainer.appendChild(span);
                }
            }

            function initDotButton(widgetId) {
                var widget = document.getElementById(widgetId);
                if (!widget) return;

                /* Split text and arrow into chars */
                var splitEls = widget.querySelectorAll('[data-split-hover]');
                splitEls.forEach(function (el) { splitIntoChars(el); });

                /* Place border dots */
                placeDots(widget);

                /* Gather all chars */
                var allChars = widget.querySelectorAll('.char');
                var isHovering = false;
                var mouseX = -9999, mouseY = -9999;
                var animId = null;

                function animate() {
                    var needsUpdate = false;
                    allChars.forEach(function (ch) {
                        if (tiltActive && isTouchDevice) {
                            var r = ch.getBoundingClientRect();
                            var normX = (r.left + r.width / 2) / window.innerWidth * 2 - 1;
                            var normY = (r.top + r.height / 2) / window.innerHeight * 2 - 1;
                            var influence = tiltGamma * normX + tiltBeta * normY;
                            influence = Math.max(0, Math.min(1, influence));
                            ch._targetWeight = MIN_WEIGHT + influence * (MAX_WEIGHT - MIN_WEIGHT);
                            needsUpdate = true;
                        } else if (isHovering) {
                            var r = ch.getBoundingClientRect();
                            var cx = r.left + r.width / 2;
                            var cy = r.top + r.height / 2;
                            var dx = mouseX - cx, dy = mouseY - cy;
                            var dist = Math.sqrt(dx * dx + dy * dy);
                            if (dist < RADIUS) {
                                var ratio = 1 - dist / RADIUS;
                                ratio = ratio * ratio;
                                ch._targetWeight = MIN_WEIGHT + ratio * (MAX_WEIGHT - MIN_WEIGHT);
                            } else {
                                ch._targetWeight = MIN_WEIGHT;
                            }
                        } else {
                            ch._targetWeight = MIN_WEIGHT;
                        }
                        ch._currentWeight = lerp(ch._currentWeight, ch._targetWeight, LERP_SPEED);
                        if (Math.abs(ch._currentWeight - ch._targetWeight) > 0.5) needsUpdate = true;
                        ch.style.fontWeight = Math.round(ch._currentWeight);
                    });
                    if (needsUpdate || isHovering || tiltActive) {
                        animId = requestAnimationFrame(animate);
                    } else {
                        animId = null;
                    }
                }

                function startAnim() { if (!animId) animId = requestAnimationFrame(animate); }

                if (!isTouchDevice) {
                    widget.addEventListener('mousemove', function (e) {
                        mouseX = e.clientX; mouseY = e.clientY;
                        isHovering = true;
                        startAnim();
                    });
                    widget.addEventListener('mouseleave', function () {
                        isHovering = false;
                        startAnim();
                    });
                }

                if (isTouchDevice) { startAnim(); }

                /* Recalculate border dots on resize */
                window.addEventListener('resize', function () {
                    placeDots(widget);
                    allChars = widget.querySelectorAll('.char');
                });

                /* Recalculate after the TINY5x3 font finishes loading.
                   Without this, the first render can use the monospace
                   fallback (different metrics → different link height),
                   so the dotted border is placed for the wrong box and
                   the text ends up looking off-center on slow devices. */
                if (document.fonts && document.fonts.ready) {
                    document.fonts.ready.then(function () {
                        placeDots(widget);
                        allChars = widget.querySelectorAll('.char');
                    });
                }
            }

            function init() {
                initDotButton('dot-button-<?php echo esc_attr($widget_id); ?>');
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            if (window.elementorFrontend) {
                window.elementorFrontend.hooks.addAction(
                    'frontend/element_ready/dot_button.default',
                    function ($scope) {
                        var w = $scope[0].querySelector('.dot-button-widget');
                        if (w) initDotButton(w.id);
                    }
                );
            }
        })();
        </script>

        <?php
    }

    protected function content_template() {}
}
