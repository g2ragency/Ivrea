<?php
if (!defined('ABSPATH')) exit;

class Elementor_Widget_Scroll_Text extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style('scroll-text-css', plugins_url('scroll-text.css', __FILE__));
    }

    public function get_style_depends() {
        return ['scroll-text-css'];
    }

    public function get_name() {
        return 'scroll_text';
    }

    public function get_title() {
        return __('Scroll Text Fill', 'elementor_addon');
    }

    public function get_icon() {
        return 'eicon-scroll';
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
            'scroll_text',
            [
                'label' => __('Testo', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'IVREA EX MACHINA NASCE PER RIATTIVARE QUELLO SPIRITO, NON IN CHIAVE CELEBRATIVA, MA COME LABORATORIO CONTEMPORANEO PERMANENTE PER ABITARE IL TEMPO CHE VIVIAMO.',
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
            'base_color',
            [
                'label' => __('Colore Base', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#cccccc',
            ]
        );

        $this->add_control(
            'fill_color',
            [
                'label' => __('Colore Riempimento', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $text = $settings['scroll_text'];
        $base_color = $settings['base_color'];
        $fill_color = $settings['fill_color'];
        $widget_id = $this->get_id();
        ?>

        <div class="scroll-text-widget" id="scroll-text-<?php echo esc_attr($widget_id); ?>" 
             data-base-color="<?php echo esc_attr($base_color); ?>" 
             data-fill-color="<?php echo esc_attr($fill_color); ?>">
            <h3 class="scroll-text-content">
                <?php echo esc_html($text); ?>
            </h3>
        </div>

        <script>
        (function () {
            "use strict";

            function initScrollText(container) {
                var paragraph = container.querySelector(".scroll-text-content");
                var baseColor = container.getAttribute("data-base-color");
                var fillColor = container.getAttribute("data-fill-color");
                var originalText = paragraph.textContent.trim();

                // Split in parole
                paragraph.innerHTML = "";
                var words = originalText.split(" ");

                words.forEach(function (word, index) {
                    var wordSpan = document.createElement("span");
                    wordSpan.classList.add("scroll-word");

                    for (var i = 0; i < word.length; i++) {
                        var charSpan = document.createElement("span");
                        charSpan.classList.add("scroll-char");
                        charSpan.textContent = word[i];
                        charSpan.style.color = baseColor;
                        wordSpan.appendChild(charSpan);
                    }

                    paragraph.appendChild(wordSpan);

                    if (index < words.length - 1) {
                        var space = document.createTextNode(" ");
                        paragraph.appendChild(space);
                    }
                });

                var chars = paragraph.querySelectorAll(".scroll-char");

                // GSAP ScrollTrigger animation
                if (typeof gsap !== "undefined" && typeof ScrollTrigger !== "undefined") {
                    gsap.registerPlugin(ScrollTrigger);

                    gsap.to(chars, {
                        color: fillColor,
                        stagger: 0.03,
                        scrollTrigger: {
                            trigger: container,
                            start: "top 60%",
                            end: "bottom 20%",
                            scrub: 2,
                            toggleActions: "play none none reverse",
                        }
                    });
                }
            }

            function init() {
                var containers = document.querySelectorAll(".scroll-text-widget");
                containers.forEach(function (container) {
                    initScrollText(container);
                });
            }

            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", init);
            } else {
                init();
            }

            if (window.elementorFrontend) {
                window.elementorFrontend.hooks.addAction(
                    "frontend/element_ready/scroll_text.default",
                    function ($scope) {
                        var container = $scope[0].querySelector(".scroll-text-widget");
                        if (container) {
                            initScrollText(container);
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
