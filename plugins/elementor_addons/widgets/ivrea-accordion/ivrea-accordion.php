<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Elementor_Widget_Ivrea_Accordion extends \Elementor\Widget_Base {

    public function get_name() {
        return 'ivrea_accordion';
    }

    public function get_title() {
        return 'Accordion Ivrea';
    }

    public function get_icon() {
        return 'eicon-accordion';
    }

    public function get_categories() {
        return [ 'ivrea-custom-addons' ];
    }

    public function get_style_depends() {
        wp_register_style('ivrea-accordion-css', plugins_url('ivrea-accordion.css', __FILE__));
        return [ 'ivrea-accordion-css' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => 'Accordion Items',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'item_title',
            [
                'label' => 'Title',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Accordion Title',
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'item_content',
            [
                'label' => 'Content',
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => 'Accordion Content',
                'show_label' => false,
            ]
        );

        $this->add_control(
            'accordion_items',
            [
                'label' => 'Items',
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'item_title' => 'WORKSHOP DI CO-PROGETTAZIONE E CREATIVITÀ',
                        'item_content' => 'Testo interno dell\'accordion di esempio.',
                    ],
                    [
                        'item_title' => 'CANTIERI DELLA MEMORIA E FORUM PUBBLICI',
                        'item_content' => 'Testo interno dell\'accordion di esempio.',
                    ],
                ],
                'title_field' => '{{{ item_title }}}',
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();

        if ( empty( $settings['accordion_items'] ) ) {
            return;
        }

        echo '<div class="ivrea-accordion-wrapper" id="ivrea-accordion-' . esc_attr($widget_id) . '">';
        echo '  <img src="/wp-content/uploads/2026/04/Line-8.svg" class="ivrea-accordion-divider" alt="" aria-hidden="true">';

        foreach ( $settings['accordion_items'] as $index => $item ) {
            $is_active = '';
            $display_style = '';

            echo '<div class="ivrea-accordion-item ' . esc_attr($is_active) . '">';
            echo '  <button class="ivrea-accordion-header">';
            echo '    <h4 class="ivrea-accordion-title">' . esc_html($item['item_title']) . '</h4>';
            echo '    <span class="ivrea-accordion-icon"><img src="/wp-content/uploads/2026/03/arrow-down-ivrea.png" alt="Toggle"></span>';
            echo '  </button>';
            echo '  <div class="ivrea-accordion-content" ' . $display_style . '>';
            echo '    <div class="ivrea-accordion-inner-wrapper"><div class="ivrea-accordion-inner">';
            echo        wp_kses_post($item['item_content']);
            echo '    </div></div>';
            echo '  </div>';
            
            // Adding the separating SVG line after each item (the top item border is usually rendered via CSS or we can do it via bottom borders of SVG)
            echo '  <img src="/wp-content/uploads/2026/04/Line-8.svg" class="ivrea-accordion-divider" alt="" aria-hidden="true">';
            echo '</div>';
        }

        echo '</div>';

        // Inline JS for simple slide up/down toggle
        ?>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            var wrapper = document.getElementById('ivrea-accordion-<?php echo esc_attr($widget_id); ?>');
            if(!wrapper) return;
            var headers = wrapper.querySelectorAll('.ivrea-accordion-header');

            headers.forEach(function(header) {
                header.addEventListener('click', function() {
                    var item = this.parentElement;
                    var content = this.nextElementSibling;
                    var isActive = item.classList.contains('active');

                    // If we want multiple open accordions, just remove the following code block.
                    // Assuming standard accordion where only 1 is open, we close others:
                    var allItems = wrapper.querySelectorAll('.ivrea-accordion-item');
                    allItems.forEach(function(otherItem) {
                        otherItem.classList.remove('active');
                        var otherContent = otherItem.querySelector('.ivrea-accordion-content');
                        
                    });

                    // Toggle current
                    if (!isActive) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
            });
        });
        </script>
        <?php
    }

}
