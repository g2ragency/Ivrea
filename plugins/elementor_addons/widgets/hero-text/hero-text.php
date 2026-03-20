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
                'default' => '#f0f0f0',
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
            <div class="hero-text-top">
                <h1 class="hero-title" style="color: <?php echo esc_attr($title_color); ?>;" data-split-hover>
                    <?php echo nl2br(esc_html($title)); ?>
                </h1>
                <div class="hero-dates" style="color: <?php echo esc_attr($dates_color); ?>;" data-split-hover>
                    <?php echo nl2br(esc_html($dates)); ?>
                </div>
            </div>
            <div class="hero-subtitle" style="color: <?php echo esc_attr($subtitle_color); ?>;" data-split-hover>
                <?php echo esc_html($subtitle); ?>
            </div>
        </div>

        <script>
        (function () {
            "use strict";

            var RADIUS = 120;
            var MIN_WEIGHT = 100;
            var MAX_WEIGHT = 900;

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
                            wordSpan.appendChild(span);
                        }

                        element.appendChild(wordSpan);

                        if (w < words.length - 1) {
                            var space = document.createElement("span");
                            space.classList.add("char", "space");
                            space.innerHTML = "&nbsp;";
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

                widget.addEventListener("mousemove", function (e) {
                    var mouseX = e.clientX;
                    var mouseY = e.clientY;

                    allChars.forEach(function (charEl) {
                        var charRect = charEl.getBoundingClientRect();
                        var charCenterX = charRect.left + charRect.width / 2;
                        var charCenterY = charRect.top + charRect.height / 2;

                        var distance = Math.sqrt(
                            Math.pow(mouseX - charCenterX, 2) + Math.pow(mouseY - charCenterY, 2)
                        );

                        var weight;
                        if (distance < RADIUS) {
                            var ratio = 1 - distance / RADIUS;
                            weight = MIN_WEIGHT + ratio * (MAX_WEIGHT - MIN_WEIGHT);
                        } else {
                            weight = MIN_WEIGHT;
                        }

                        charEl.style.fontWeight = Math.round(weight);
                    });
                });

                widget.addEventListener("mouseleave", function () {
                    allChars.forEach(function (charEl) {
                        charEl.style.fontWeight = "";
                    });
                });
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
