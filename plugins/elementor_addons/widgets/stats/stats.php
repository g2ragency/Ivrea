<?php
if (!defined('ABSPATH')) exit;

class Elementor_Widget_Stats extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        wp_register_style('stats-css', plugins_url('stats.css', __FILE__));
    }

    public function get_style_depends() {
        return ['stats-css'];
    }

    public function get_name() {
        return 'stats_widget';
    }

    public function get_title() {
        return __('Stats', 'elementor_addon');
    }

    public function get_icon() {
        return 'eicon-number-field';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Dati', 'elementor_addon'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        for ($i = 1; $i <= 4; $i++) {
            $this->add_control(
                'stat_number_' . $i,
                [
                    'label' => __('Numero ' . $i, 'elementor_addon'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => $i == 1 ? '3' : ($i == 2 ? '15' : ($i == 3 ? '8' : '24')),
                    'label_block' => false,
                ]
            );

            $this->add_control(
                'stat_label_' . $i,
                [
                    'label' => __('Label ' . $i, 'elementor_addon'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => $i == 1 ? 'LOCATION' : ($i == 2 ? 'OSPITI' : ($i == 3 ? 'TEMATICHE' : 'ORE')),
                    'label_block' => false,
                    'separator' => 'after',
                ]
            );
        }

        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Stile', 'elementor_addon'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'number_color',
            [
                'label' => __('Colore numeri', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label' => __('Colore label', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#737373',
            ]
        );

        $this->add_control(
            'separator_color',
            [
                'label' => __('Colore separatori', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#cccccc',
            ]
        );

        $this->add_control(
            'number_size',
            [
                'label' => __('Font Size numeri (px)', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 160,
                'min' => 40,
                'max' => 300,
            ]
        );

        $this->add_control(
            'label_size',
            [
                'label' => __('Font Size label (px)', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 40,
                'min' => 10,
                'max' => 80,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();

        $number_color = $settings['number_color'];
        $label_color = $settings['label_color'];
        $separator_color = $settings['separator_color'];
        $number_size = $settings['number_size'];
        $label_size = $settings['label_size'];
        ?>

        <div class="stats-widget" id="stats-<?php echo esc_attr($widget_id); ?>">
            <div class="stats-grid">
                <?php for ($i = 1; $i <= 4; $i++) :
                    $number = $settings['stat_number_' . $i];
                    $label = $settings['stat_label_' . $i];
                ?>
                    <?php if ($i > 1) : ?>
                        <div class="stats-separator" aria-hidden="true">
                            <span class="stats-separator-dots" style="color: <?php echo esc_attr($separator_color); ?>;" data-stats-separator></span>
                        </div>
                    <?php endif; ?>
                    <div class="stats-item">
                        <span class="stats-number" style="color: <?php echo esc_attr($number_color); ?>; font-size: <?php echo esc_attr($number_size); ?>px;"><?php echo esc_html($number); ?></span>
                        <span class="stats-label" style="color: <?php echo esc_attr($label_color); ?>; font-size: <?php echo esc_attr($label_size); ?>px;"><?php echo esc_html($label); ?></span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <script>
        (function () {
            "use strict";

            function fillSeparatorDots(widget) {
                var separators = widget.querySelectorAll('[data-stats-separator]');
                if (separators.length === 0) return;

                var dotSize = 18;
                var spacing = dotSize * 0.38;

                /* Find the max length among all separators */
                var maxLength = 0;
                separators.forEach(function (el) {
                    var parent = el.closest('.stats-separator');
                    var isHorizontal = parent.classList.contains('is-horizontal');
                    var parentRect = parent.getBoundingClientRect();
                    var length = isHorizontal ? parentRect.width : parentRect.height;
                    if (length > maxLength) maxLength = length;
                });

                var count = Math.max(1, Math.floor(maxLength / spacing));

                separators.forEach(function (el) {
                    el.innerHTML = '';
                    for (var i = 0; i < count; i++) {
                        var span = document.createElement('span');
                        span.classList.add('dot');
                        span.textContent = '.';
                        el.appendChild(span);
                    }
                });
            }

            function checkLayout(widget) {
                var separators = widget.querySelectorAll('.stats-separator');
                var width = window.innerWidth;
                separators.forEach(function (sep) {
                    if (width <= 768) {
                        sep.classList.add('is-horizontal');
                        sep.classList.remove('is-vertical');
                    } else {
                        sep.classList.remove('is-horizontal');
                        sep.classList.add('is-vertical');
                    }
                });
                fillSeparatorDots(widget);
            }

            function init() {
                var widget = document.getElementById('stats-<?php echo esc_attr($widget_id); ?>');
                if (!widget) return;
                checkLayout(widget);
                window.addEventListener('resize', function () { checkLayout(widget); });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            if (window.elementorFrontend) {
                window.elementorFrontend.hooks.addAction(
                    'frontend/element_ready/stats_widget.default',
                    function ($scope) {
                        var w = $scope[0].querySelector('.stats-widget');
                        if (w) {
                            checkLayout(w);
                            window.addEventListener('resize', function () { checkLayout(w); });
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
