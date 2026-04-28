<?php
if (!defined('ABSPATH')) exit;

class Elementor_Widget_Download_Cards extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        wp_register_style('download-cards-css', plugins_url('download-cards.css', __FILE__));
    }

    public function get_style_depends() {
        return ['download-cards-css'];
    }

    public function get_name() {
        return 'download_cards';
    }

    public function get_title() {
        return __('Download Cards Grid', 'elementor_addon');
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
                'label' => __('Download Cards', 'elementor_addon'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'card_title',
            [
                'label' => __('Testo (es. Nome del documento)', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'QUI INSERIRE IL NOME DEL DOCUMENTO CHE SI PUÒ SCARICARE',
            ]
        );

        $repeater->add_control(
            'card_link',
            [
                'label' => __('Link (Download)', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'elementor_addon'),
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                    'custom_attributes' => '',
                ],
            ]
        );

        $this->add_control(
            'cards',
            [
                'label' => __('Cards', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'card_title' => 'QUI INSERIRE IL NOME DEL DOCUMENTO CHE SI PUÒ SCARICARE',
                    ],
                    [
                        'card_title' => 'QUI INSERIRE IL NOME DEL DOCUMENTO CHE SI PUÒ SCARICARE',
                    ],
                    [
                        'card_title' => 'QUI INSERIRE IL NOME DEL DOCUMENTO CHE SI PUÒ SCARICARE',
                    ],
                ],
                'title_field' => '{{{ card_title }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if (empty($settings['cards'])) {
            return;
        }

        echo '<div class="download-cards-grid">';
        
        foreach ($settings['cards'] as $index => $item) {
            $link_url = !empty($item['card_link']['url']) ? $item['card_link']['url'] : '#';
            $target = $item['card_link']['is_external'] ? ' target="_blank"' : '';
            $nofollow = $item['card_link']['nofollow'] ? ' rel="nofollow"' : '';
            
            echo '<a href="' . esc_url($link_url) . '"' . $target . $nofollow . ' class="download-card-item">';
            echo '  <p class="download-card-title">' . nl2br(esc_html($item['card_title'])) . '</p>';
            echo '  <div class="download-card-icon">';
            echo '    <img src="/wp-content/uploads/2026/03/arrow-down-ivrea.png" alt="Download">';
            echo '  </div>';
            echo '</a>';
        }

        echo '</div>';
    }
}
