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
                                            <span class="arrow-text" data-split-hover>→</span>
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

            /* ---- Arrow hover effect (variable font weight) ---- */
            var ARROW_RADIUS = 200;
            var ARROW_LERP = 0.08;
            var ARROW_MIN_WEIGHT = 80;
            var ARROW_MAX_WEIGHT = 240;

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

            function splitArrowChars(el) {
                var text = el.textContent;
                el.innerHTML = '';
                for (var i = 0; i < text.length; i++) {
                    var span = document.createElement('span');
                    span.classList.add('char');
                    span.textContent = text[i];
                    span._currentWeight = ARROW_MIN_WEIGHT;
                    span._targetWeight = ARROW_MIN_WEIGHT;
                    el.appendChild(span);
                }
            }

            function initArrowHover(section) {
                var arrows = section.querySelectorAll('.arrow-text[data-split-hover]');
                arrows.forEach(function (arrow) { splitArrowChars(arrow); });

                var allChars = section.querySelectorAll('.horizontal-card-arrow .char');
                if (allChars.length === 0) return;

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
                            ch._targetWeight = ARROW_MIN_WEIGHT + influence * (ARROW_MAX_WEIGHT - ARROW_MIN_WEIGHT);
                            needsUpdate = true;
                        } else if (isHovering) {
                            var r = ch.getBoundingClientRect();
                            var cx = r.left + r.width / 2;
                            var cy = r.top + r.height / 2;
                            var dx = mouseX - cx, dy = mouseY - cy;
                            var dist = Math.sqrt(dx * dx + dy * dy);
                            if (dist < ARROW_RADIUS) {
                                var ratio = 1 - dist / ARROW_RADIUS;
                                ratio = ratio * ratio;
                                ch._targetWeight = ARROW_MIN_WEIGHT + ratio * (ARROW_MAX_WEIGHT - ARROW_MIN_WEIGHT);
                            } else {
                                ch._targetWeight = ARROW_MIN_WEIGHT;
                            }
                        } else {
                            ch._targetWeight = ARROW_MIN_WEIGHT;
                        }
                        ch._currentWeight = lerp(ch._currentWeight, ch._targetWeight, ARROW_LERP);
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
                    section.addEventListener('mousemove', function (e) {
                        mouseX = e.clientX; mouseY = e.clientY;
                        isHovering = true;
                        startAnim();
                    });
                    section.addEventListener('mouseleave', function () {
                        isHovering = false;
                        startAnim();
                    });
                }

                if (isTouchDevice) { startAnim(); }
            }

            /* ---- Horizontal scroll logic ---- */
            function initHorizontalCards(sectionId) {
                var section = document.getElementById(sectionId);
                if (!section) return;

                /* Init arrow hover effect (always) */
                initArrowHover(section);

                /* On mobile: skip horizontal scroll, cards stack vertically via CSS */
                if (window.innerWidth <= 768) return;

                if (!window.gsap || !window.ScrollTrigger) return;
                gsap.registerPlugin(ScrollTrigger);

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
