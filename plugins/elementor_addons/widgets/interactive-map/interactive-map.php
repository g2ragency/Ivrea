<?php
if (!defined('ABSPATH')) exit;

class Elementor_Widget_Interactive_Map extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        wp_register_style('interactive-map-css', plugins_url('interactive-map.css', __FILE__));
    }

    public function get_style_depends() {
        return ['interactive-map-css'];
    }

    public function get_name() {
        return 'interactive_map';
    }

    public function get_title() {
        return __('Interactive Map', 'elementor_addon');
    }

    public function get_icon() {
        return 'eicon-map-pin';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        // I controlli del widget possono essere inseriti qui in seguito
    }

    protected function render() {
        ?>
        <div class="ivrea-map-wrap">
            <img src="/wp-content/uploads/2026/04/Mappa-Ivrea-No-Flag.svg" alt="Mappa Ivrea" class="ivrea-map-bg">

            <!-- Punto 1: OFFICINE H -->
            <div class="ivrea-map-pin" style="top: 45%; left: 45%;">
                <img src="/wp-content/uploads/2026/04/Flag.svg" alt="Bandiera" class="flag-svg">
                <div class="ivrea-map-info">
                    <div class="ivrea-map-date">20 Giugno</div>
                    <h4 class="ivrea-map-title">OFFICINE H</h4>
                    <div class="ivrea-map-street">Via Beneficio di Santa Lucia<br>10015 Ivrea TO</div>
                </div>
            </div>

            <!-- Punto 2: FABBRICA DEI MATTONI ROSSI -->
            <div class="ivrea-map-pin" style="top: 87%; left: 45%;">
                <img src="/wp-content/uploads/2026/04/Flag.svg" alt="Bandiera" class="flag-svg">
                <div class="ivrea-map-info">
                    <div class="ivrea-map-date">21 Giugno</div>
                    <h4 class="ivrea-map-title">FABBRICA DEI<br>MATTONI ROSSI</h4>
                    <div class="ivrea-map-street">Via Guglielmo Jervis, 16<br>10015 Ivrea TO</div>
                </div>
            </div>

            <!-- Punto 3:  MUSEO TECNOLOGIC@METE-->
            <div class="ivrea-map-pin" style="top: 50%; left: 57%;">
                <img src="/wp-content/uploads/2026/04/Flag.svg" alt="Bandiera" class="flag-svg">
                <div class="ivrea-map-info">
                    <div class="ivrea-map-date">19 Giugno</div>
                    <h4 class="ivrea-map-title">MUSEO<br>TECNLOGIC@METE</h4>
                    <div class="ivrea-map-street">Via Giuseppe di Vittorio, 29<br>10015 Ivrea TO</div>
                </div>
            </div>

            <!-- Punto 4: Punto rosso -->
            <div class="ivrea-map-pin ivrea-map-pin--dot" style="top: 15%; left: 59%;">
                <span class="ivrea-dot" style="background:#FF3333;"></span>
            </div>
        </div>
        <?php
    }
}