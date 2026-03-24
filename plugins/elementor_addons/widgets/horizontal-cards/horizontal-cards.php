<?php
if (!defined('ABSPATH')) exit;

class Elementor_Widget_Horizontal_Cards extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style('horizontal-cards-css', plugins_url('horizontal-cards.css', __FILE__));
    }

    public function get_style_depends() {
        return ['horizontal-cards-css'];
    }

    public function get_name() {
        return 'horizontal_scroll_cards';
    }

    public function get_title() {
        return __('Horizontal Cards', 'elementor_addon');
    }

    public function get_icon() {
        return 'eicon-h-align-stretch';
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
            'card_background',
            [
                'label' => __('Colore sfondo', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#d9d9d9',
            ]
        );

        $repeater->add_control(
            'card_title',
            [
                'label' => __('Titolo', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'FESTA FINALE: IVREA EX MUSICA',
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'card_description',
            [
                'label' => __('Descrizione', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Il grande momento di restituzione collettiva che trasforma la riflessione tecnologica in celebrazione comunitaria.',
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'card_image',
            [
                'label' => __('Immagine', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'card_link',
            [
                'label' => __('Link card', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => 'https://example.com',
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => false,
                    'nofollow' => false,
                ],
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
                        'card_background' => '#d9d9d9',
                        'card_title' => 'FESTA FINALE: IVREA EX MUSICA',
                        'card_description' => 'Il grande momento di restituzione collettiva che trasforma la riflessione tecnologica in celebrazione comunitaria.',
                    ],
                    [
                        'card_background' => '#ff3333',
                        'card_title' => 'GRANDI PANEL E KEYNOTE SPEECH',
                        'card_description' => 'Incontri di respiro internazionale con pionieri della tecnologia e protagonisti dell’innovazione.',
                    ],
                    [
                        'card_background' => '#0bb47b',
                        'card_title' => 'HACKATHON SOCIALE',
                        'card_description' => 'Una maratona di tre giorni per progettare proposte concrete su competenze, lavoro e welfare.',
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

        if (empty($settings['cards_list']) || !is_array($settings['cards_list'])) {
            return;
        }
        ?>

        <section class="horizontal-cards-section" id="horizontal-cards-<?php echo esc_attr($widget_id); ?>">
            <div class="horizontal-cards-track">
                    <?php foreach ($settings['cards_list'] as $card) :
                        $card_bg = !empty($card['card_background']) ? $card['card_background'] : '#d9d9d9';
                        $title = !empty($card['card_title']) ? $card['card_title'] : '';
                        $description = !empty($card['card_description']) ? $card['card_description'] : '';
                        $image_url = !empty($card['card_image']['url']) ? $card['card_image']['url'] : '';
                        $link = !empty($card['card_link']) && is_array($card['card_link']) ? $card['card_link'] : [];
                        $has_link = !empty($link['url']);

                        $target = !empty($link['is_external']) ? ' target="_blank"' : '';
                        $rel_values = [];
                        if (!empty($link['nofollow'])) {
                            $rel_values[] = 'nofollow';
                        }
                        if (!empty($link['is_external'])) {
                            $rel_values[] = 'noopener';
                        }
                        $rel_attr = !empty($rel_values) ? ' rel="' . esc_attr(implode(' ', $rel_values)) . '"' : '';

                        $tag = $has_link ? 'a' : 'div';
                        $link_attr = $has_link ? ' href="' . esc_url($link['url']) . '"' . $target . $rel_attr : '';
                    ?>
                        <article class="horizontal-card" style="background-color: <?php echo esc_attr($card_bg); ?>;">
                            <<?php echo $tag; ?> class="horizontal-card-link"<?php echo $link_attr; ?>>
                                <div class="horizontal-card-layout">
                                    <div class="horizontal-card-content">
                                        <h2 class="horizontal-card-title"><?php echo esc_html($title); ?></h2>
                                        <p class="horizontal-card-description"><?php echo esc_html($description); ?></p>

                                        <div class="horizontal-card-arrow" aria-hidden="true">
                                            <svg width="40" height="56" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M33.6 16.8C33.6 17.3134 33.4834 17.78 33.25 18.2C32.97 18.62 32.62 18.97 32.2 19.25C31.78 19.4834 31.3134 19.6 30.8 19.6C30.2867 19.6 29.82 19.4834 29.4 19.25C28.98 18.97 28.6534 18.62 28.42 18.2C28.14 17.78 28 17.3134 28 16.8C28 16.2867 28.14 15.82 28.42 15.4C28.6534 14.98 28.98 14.6534 29.4 14.42C29.82 14.14 30.2867 14 30.8 14C31.3134 14 31.78 14.14 32.2 14.42C32.62 14.6534 32.97 14.98 33.25 15.4C33.4834 15.82 33.6 16.2867 33.6 16.8ZM26.6 23.8C26.6 24.3134 26.4834 24.78 26.25 25.2C25.97 25.62 25.62 25.97 25.2 26.25C24.78 26.4834 24.3134 26.6 23.8 26.6C23.2867 26.6 22.82 26.4834 22.4 26.25C21.98 25.97 21.6534 25.62 21.42 25.2C21.14 24.78 21 24.3134 21 23.8C21 23.2867 21.14 22.82 21.42 22.4C21.6534 21.98 21.98 21.6534 22.4 21.42C22.82 21.14 23.2867 21 23.8 21C24.3134 21 24.78 21.14 25.2 21.42C25.62 21.6534 25.97 21.98 26.25 22.4C26.4834 22.82 26.6 23.2867 26.6 23.8ZM5.60005 16.8C5.60005 17.3134 5.48338 17.78 5.25005 18.2C4.97005 18.62 4.62005 18.97 4.20005 19.25C3.78005 19.4834 3.31338 19.6 2.80005 19.6C2.28671 19.6 1.82005 19.4834 1.40005 19.25C0.980046 18.97 0.653379 18.62 0.420046 18.2C0.140047 17.78 4.69585e-05 17.3134 4.6936e-05 16.8C4.69136e-05 16.2867 0.140046 15.82 0.420046 15.4C0.653379 14.98 0.980045 14.6534 1.40005 14.42C1.82005 14.14 2.28671 14 2.80005 14C3.31338 14 3.78005 14.14 4.20005 14.42C4.62005 14.6534 4.97005 14.98 5.25005 15.4C5.48338 15.82 5.60005 16.2867 5.60005 16.8ZM26.6 9.80005C26.6 10.3134 26.4834 10.78 26.25 11.2C25.97 11.62 25.62 11.97 25.2 12.25C24.78 12.4834 24.3134 12.6 23.8 12.6C23.2867 12.6 22.82 12.4834 22.4 12.25C21.98 11.97 21.6534 11.62 21.42 11.2C21.14 10.78 21 10.3134 21 9.80005C21 9.28671 21.14 8.82004 21.42 8.40004C21.6534 7.98004 21.98 7.65338 22.4 7.42004C22.82 7.14005 23.2867 7.00005 23.8 7.00005C24.3134 7.00005 24.78 7.14005 25.2 7.42004C25.62 7.65338 25.97 7.98004 26.25 8.40004C26.4834 8.82004 26.6 9.28671 26.6 9.80005ZM19.6 2.80005C19.6 3.31338 19.4834 3.78004 19.25 4.20004C18.97 4.62004 18.62 4.97005 18.2 5.25005C17.78 5.48338 17.3134 5.60005 16.8 5.60005C16.2867 5.60005 15.82 5.48338 15.4 5.25005C14.98 4.97005 14.6534 4.62005 14.42 4.20004C14.14 3.78004 14 3.31338 14 2.80005C14 2.28671 14.14 1.82005 14.42 1.40005C14.6534 0.980047 14.98 0.653381 15.4 0.420048C15.82 0.140046 16.2867 4.46087e-05 16.8 4.45862e-05C17.3134 4.45638e-05 17.78 0.140046 18.2 0.420047C18.62 0.653381 18.97 0.980047 19.25 1.40005C19.4834 1.82005 19.6 2.28671 19.6 2.80005ZM26.6 16.8C26.6 17.3134 26.4834 17.78 26.25 18.2C25.97 18.62 25.62 18.97 25.2 19.25C24.78 19.4834 24.3134 19.6 23.8 19.6C23.2867 19.6 22.82 19.4834 22.4 19.25C21.98 18.97 21.6534 18.62 21.42 18.2C21.14 17.78 21 17.3134 21 16.8C21 16.2867 21.14 15.82 21.42 15.4C21.6534 14.98 21.98 14.6534 22.4 14.42C22.82 14.14 23.2867 14 23.8 14C24.3134 14 24.78 14.14 25.2 14.42C25.62 14.6534 25.97 14.98 26.25 15.4C26.4834 15.82 26.6 16.2867 26.6 16.8ZM12.6 16.8C12.6 17.3134 12.4834 17.78 12.25 18.2C11.97 18.62 11.62 18.97 11.2 19.25C10.78 19.4834 10.3134 19.6 9.80005 19.6C9.28671 19.6 8.82005 19.4834 8.40005 19.25C7.98005 18.97 7.65338 18.62 7.42005 18.2C7.14005 17.78 7.00005 17.3134 7.00005 16.8C7.00005 16.2867 7.14005 15.82 7.42005 15.4C7.65338 14.98 7.98005 14.6534 8.40005 14.42C8.82005 14.14 9.28671 14 9.80005 14C10.3134 14 10.78 14.14 11.2 14.42C11.62 14.6534 11.97 14.98 12.25 15.4C12.4834 15.82 12.6 16.2867 12.6 16.8ZM19.6 16.8C19.6 17.3134 19.4834 17.78 19.25 18.2C18.97 18.62 18.62 18.97 18.2 19.25C17.78 19.4834 17.3134 19.6 16.8 19.6C16.2867 19.6 15.82 19.4834 15.4 19.25C14.98 18.97 14.6534 18.62 14.42 18.2C14.14 17.78 14 17.3134 14 16.8C14 16.2867 14.14 15.82 14.42 15.4C14.6534 14.98 14.98 14.6534 15.4 14.42C15.82 14.14 16.2867 14 16.8 14C17.3134 14 17.78 14.14 18.2 14.42C18.62 14.6534 18.97 14.98 19.25 15.4C19.4834 15.82 19.6 16.2867 19.6 16.8ZM19.6 30.8C19.6 31.3134 19.4834 31.78 19.25 32.2C18.97 32.62 18.62 32.97 18.2 33.25C17.78 33.4834 17.3134 33.6 16.8 33.6C16.2867 33.6 15.82 33.4834 15.4 33.25C14.98 32.97 14.6534 32.62 14.42 32.2C14.14 31.78 14 31.3134 14 30.8C14 30.2867 14.14 29.82 14.42 29.4C14.6534 28.98 14.98 28.6534 15.4 28.42C15.82 28.14 16.2867 28 16.8 28C17.3134 28 17.78 28.14 18.2 28.42C18.62 28.6534 18.97 28.98 19.25 29.4C19.4834 29.82 19.6 30.2867 19.6 30.8Z" fill="currentColor"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="horizontal-card-image<?php echo empty($image_url) ? ' is-empty' : ''; ?>">
                                        <?php if (!empty($image_url)) : ?>
                                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </<?php echo $tag; ?>>
                        </article>
                    <?php endforeach; ?>
                </div>
        </section>

        <script>
        (function () {
            "use strict";

            function initHorizontalCards(sectionId) {
                if (!window.gsap || !window.ScrollTrigger) return;
                gsap.registerPlugin(ScrollTrigger);

                var section = document.getElementById(sectionId);
                if (!section) return;

                var track = section.querySelector('.horizontal-cards-track');
                if (!track) return;

                var cards = Array.prototype.slice.call(track.querySelectorAll('.horizontal-card'));
                if (cards.length === 0) return;

                /* Remove overflow:hidden from ALL Elementor ancestors */
                var el = section.parentElement;
                while (el && el !== document.body) {
                    el.style.overflow = 'visible';
                    el = el.parentElement;
                }

                /* Config */
                var CARD_GAP = 12;
                var STACKED_WIDTH = 50; /* Width of a compressed card strip */
                var CARD_WIDTH = Math.min(1180, window.innerWidth * 0.92);
                var LEFT_PAD = window.innerWidth >= 1360
                    ? (window.innerWidth - 1360) / 2
                    : window.innerWidth * 0.04;

                /* Total scroll needed:
                   At progress=1, the last card should sit at LEFT_PAD + (n-1)*STACKED_WIDTH
                   Its natural position is LEFT_PAD + (n-1)*(CARD_WIDTH+CARD_GAP)
                   So scrollLength = naturalLeft_last - minLeft_last */
                var lastNatural = LEFT_PAD + (cards.length - 1) * (CARD_WIDTH + CARD_GAP);
                var lastMinLeft = LEFT_PAD + (cards.length - 1) * STACKED_WIDTH;
                var scrollLength = lastNatural - lastMinLeft;

                /* Create spacer */
                var spacer = document.createElement('div');
                spacer.className = 'horizontal-cards-spacer';
                spacer.style.height = (window.innerHeight + scrollLength) + 'px';
                spacer.style.position = 'relative';
                section.parentNode.insertBefore(spacer, section);
                spacer.appendChild(section);

                /* State */
                var isFixed = false;
                var isDone = false;

                function onScroll() {
                    var spacerRect = spacer.getBoundingClientRect();
                    var spacerTop = spacerRect.top;
                    var spacerBottom = spacerRect.bottom;

                    /* Pin logic */
                    if (spacerTop <= 0 && spacerBottom > window.innerHeight) {
                        if (!isFixed) {
                            section.style.position = 'fixed';
                            section.style.top = '0';
                            section.style.left = '0';
                            section.style.width = '100vw';
                            section.style.zIndex = '10';
                            isFixed = true;
                            isDone = false;
                        }
                        var progress = Math.max(0, Math.min(1, -spacerTop / scrollLength));
                        positionCards(progress);
                    } else if (spacerBottom <= window.innerHeight) {
                        if (isFixed || !isDone) {
                            section.style.position = 'absolute';
                            section.style.top = 'auto';
                            section.style.bottom = '0';
                            section.style.left = '0';
                            section.style.width = '100vw';
                            section.style.zIndex = '';
                            isFixed = false;
                            isDone = true;
                        }
                        positionCards(1);
                    } else {
                        if (isFixed || isDone) {
                            section.style.position = '';
                            section.style.top = '';
                            section.style.left = '';
                            section.style.bottom = '';
                            section.style.width = '';
                            section.style.zIndex = '';
                            isFixed = false;
                            isDone = false;
                        }
                        positionCards(0);
                    }
                }

                function positionCards(progress) {
                    var scrollX = progress * scrollLength;

                    for (var i = 0; i < cards.length; i++) {
                        var card = cards[i];
                        /* Natural position of this card */
                        var naturalLeft = LEFT_PAD + i * (CARD_WIDTH + CARD_GAP);
                        /* Minimum left: card stops here, showing STACKED_WIDTH strip */
                        var minLeft = LEFT_PAD + i * STACKED_WIDTH;
                        /* Current scrolled position */
                        var currentLeft = naturalLeft - scrollX;
                        /* Card never goes further left than its stack position */
                        var finalLeft = Math.max(currentLeft, minLeft);

                        card.style.left = finalLeft + 'px';
                        card.style.width = CARD_WIDTH + 'px';
                        /* Later cards (higher index) sit on top of earlier cards */
                        card.style.zIndex = i;
                    }
                }

                /* Initial positioning */
                window.addEventListener('scroll', onScroll, { passive: true });
                onScroll();

                /* Refresh on image load */
                section.querySelectorAll('img').forEach(function (img) {
                    if (!img.complete) {
                        img.addEventListener('load', function () {
                            CARD_WIDTH = Math.min(1180, window.innerWidth * 0.92);
                            lastNatural = LEFT_PAD + (cards.length - 1) * (CARD_WIDTH + CARD_GAP);
                            lastMinLeft = LEFT_PAD + (cards.length - 1) * STACKED_WIDTH;
                            scrollLength = lastNatural - lastMinLeft;
                            spacer.style.height = (window.innerHeight + scrollLength) + 'px';
                        }, { once: true });
                    }
                });

                /* Handle resize */
                window.addEventListener('resize', function () {
                    CARD_WIDTH = Math.min(1180, window.innerWidth * 0.92);
                    LEFT_PAD = window.innerWidth >= 1360
                        ? (window.innerWidth - 1360) / 2
                        : window.innerWidth * 0.04;
                    lastNatural = LEFT_PAD + (cards.length - 1) * (CARD_WIDTH + CARD_GAP);
                    lastMinLeft = LEFT_PAD + (cards.length - 1) * STACKED_WIDTH;
                    scrollLength = lastNatural - lastMinLeft;
                    spacer.style.height = (window.innerHeight + scrollLength) + 'px';
                    onScroll();
                });
            }

            window.addEventListener('load', function () {
                initHorizontalCards('horizontal-cards-<?php echo esc_attr($widget_id); ?>');
            });
        })();
        </script>

        <?php
    }
}
