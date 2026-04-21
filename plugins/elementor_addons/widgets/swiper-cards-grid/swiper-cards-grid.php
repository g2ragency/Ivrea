<?php
if (!defined('ABSPATH')) exit;

class Elementor_Widget_Swiper_Cards_Grid extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style('swiper-cards-grid-css', plugins_url('swiper-cards-grid.css', __FILE__));
    }

    public function get_style_depends() {
        return ['swiper-cards-grid-css'];
    }

    public function get_name() {
        return 'swiper_cards_grid';
    }

    public function get_title() {
        return __('Swiper Cards Grid', 'elementor_addon');
    }

    public function get_icon() {
        return 'eicon-slides';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Cards', 'elementor_addon'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'card_title',
            [
                'label' => __('Titolo', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'FAVORIRE',
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'card_description',
            [
                'label' => __('Descrizione', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Promuovere un dialogo sistemico tra istituzioni, imprese, accademia e cittadinanza.',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'cards_list',
            [
                'label' => __('Cards', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'card_title' => 'FAVORIRE',
                        'card_description' => 'Promuovere un dialogo sistemico tra istituzioni, imprese, accademia e cittadinanza per definire modelli di sviluppo tecnologico orientati alla qualità del lavoro e all\'inclusione sociale, per rafforzare il legame tra innovazione e democrazia.',
                    ],
                    [
                        'card_title' => 'COINVOLGERE',
                        'card_description' => 'Attivare il protagonismo delle nuove generazioni e degli studenti nei processi di innovazione sociale, rendendoli attori consapevoli della trasformazione digitale attraverso percorsi educativi e partecipazione all\'hackathon sociale.',
                    ],
                    [
                        'card_title' => 'GENERARE',
                        'card_description' => 'Elaborare proposte concrete e innovative su temi cruciali come il welfare, le competenze e il lavoro dignitoso, traducendo la riflessione in prototipi concettuali capaci di rispondere alle sfide della transizione digitale.',
                    ],
                ],
                'title_field' => '{{{ card_title }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        ?>

        <div class="swiper-cards-grid-widget" id="swiper-cards-grid-<?php echo esc_attr($widget_id); ?>">
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($settings['cards_list'] as $index => $card) : ?>
                        <div class="swiper-slide">
                            <div class="swiper-card">
                                <h3 class="swiper-card-title"><?php echo esc_html($card['card_title']); ?></h3>
                                <p class="swiper-card-description"><?php echo esc_html($card['card_description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="swiper-controls">
                <div class="swiper-mobile-progress"><div class="swiper-mobile-progress-fill"></div></div>
            </div>
        </div>

        <script>
        (function () {
            "use strict";

            function initSwiperCardsGrid(container) {
                var swiperEl = container.querySelector(".swiper");
                if (!swiperEl) return;
                
                var progressFill = container.querySelector(".swiper-mobile-progress-fill");
                var swiperInstance = null;

                function checkBreakpoint() {
                    var mq = window.matchMedia('(max-width: 768px)');
                    if (mq.matches) {
                        if (!swiperInstance) {
                            swiperInstance = new Swiper(swiperEl, {
                                slidesPerView: "auto",
                                spaceBetween: 16,
                                freeMode: true,
                                grabCursor: true,
                                on: {
                                    progress: function(swiper, progress) {
                                        if (progressFill) {
                                            progressFill.style.width = (Math.min(1, Math.max(0, progress)) * 100) + "%";
                                        }
                                    }
                                }
                            });
                        }
                    } else {
                        if (swiperInstance) {
                            swiperInstance.destroy(true, true);
                            swiperInstance = null;
                        }
                    }
                }

                checkBreakpoint();
                window.addEventListener('resize', checkBreakpoint);
            }

            function init() {
                var container = document.getElementById("swiper-cards-grid-<?php echo esc_attr($widget_id); ?>");
                if (container) {
                    initSwiperCardsGrid(container);
                }
            }

            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", init);
            } else {
                init();
            }

            if (window.elementorFrontend) {
                window.elementorFrontend.hooks.addAction(
                    "frontend/element_ready/swiper_cards_grid.default",
                    function ($scope) {
                        var container = $scope[0].querySelector(".swiper-cards-grid-widget");
                        if (container) {
                            initSwiperCardsGrid(container);
                        }
                    }
                );
            }
        })();
        </script>

        <?php
    }
}
