<?php
if (!defined('ABSPATH')) exit;

class Elementor_Widget_Grid_Ospiti extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        wp_register_style('grid-ospiti-css', plugins_url('grid-ospiti.css', __FILE__));
        wp_register_script('grid-ospiti-js', plugins_url('grid-ospiti.js', __FILE__), ['jquery'], null, true);
    }

    public function get_style_depends() {
        return ['grid-ospiti-css'];
    }

    public function get_script_depends() {
        return ['grid-ospiti-js'];
    }

    public function get_name() {
        return 'grid_ospiti';
    }

    public function get_title() {
        return __('Grid Ospiti (Accordion)', 'elementor_addon');
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Ospiti', 'elementor_addon'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'guest_image',
            [
                'label' => __('Foto', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'guest_name',
            [
                'label' => __('Nome', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'NOME COGNOME',
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'guest_job',
            [
                'label' => __('Job Title', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Qui inserire Job Title',
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'guest_info',
            [
                'label' => __('Info evento (basso a sinistra)', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => '<p>SABATO 20 GIUGNO<br>ORE 20:30<br>OFFICINE H</p>',
            ]
        );

        $repeater->add_control(
            'guest_bio',
            [
                'label' => __('Biografia (destra)', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>',
            ]
        );

        $this->add_control(
            'guests_list',
            [
                'label' => __('Ospiti', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'guest_name' => 'FEDERICO FAGGIN',
                        'guest_job' => 'Qui inserire Job Title',
                    ],
                    [
                        'guest_name' => 'MASSIMO BANZI',
                        'guest_job' => 'Qui inserire Job Title',
                    ],
                    [
                        'guest_name' => 'LUCIANO FLORIDI',
                        'guest_job' => 'Qui inserire Job Title',
                    ],
                ],
                'title_field' => '{{{ guest_name }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        ?>

        <div class="grid-ospiti-widget" id="grid-ospiti-<?php echo esc_attr($widget_id); ?>">
            <div class="grid-container">
                <?php foreach ($settings['guests_list'] as $index => $guest) : ?>
                    <div class="ospite-grid-card" data-index="<?php echo esc_attr($index); ?>">
                        <div class="ospite-image">
                            <?php if (!empty($guest['guest_image']['url'])) : ?>
                                <img src="<?php echo esc_url($guest['guest_image']['url']); ?>" alt="<?php echo esc_attr($guest['guest_name']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="ospite-card-bottom">
                            <div>
                                <h4 class="ospite-name"><?php echo esc_html($guest['guest_name']); ?></h4>
                                <p class="ospite-job"><?php echo esc_html($guest['guest_job']); ?></p>
                            </div>
                            <div class="ospite-toggle-icon">
                                <span>↓</span>
                            </div>
                        </div>

                        <!-- Hidden Data for JS -->
                        <div class="ospite-hidden-data" style="display:none;">
                            <div class="data-name"><?php echo esc_html($guest['guest_name']); ?></div>
                            <div class="data-job"><?php echo esc_html($guest['guest_job']); ?></div>
                            <div class="data-info"><?php echo wp_kses_post($guest['guest_info']); ?></div>
                            <div class="data-bio"><?php echo wp_kses_post($guest['guest_bio']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Global Expandable Box (used to insert after the row) -->
            <div class="guest-details-box" style="display:none; overflow: hidden; height: 0;">
                <div class="guest-details-inner">
                    <div class="guest-details-left">
                        <div class="gd-top">
                            <h4 class="gd-name"></h4>
                            <p class="gd-job"></p>
                        </div>
                        <div class="gd-bottom">
                            <div class="gd-info"></div>
                        </div>
                    </div>
                    <div class="guest-details-right">
                        <div class="gd-bio"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
