<?php
if (!defined('ABSPATH')) exit; 

class Elementor_Widget_Countdown extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        
        wp_register_style('countdown-css', plugins_url('countdown.css', __FILE__));
    }

    public function get_style_depends() {
        return ['countdown-css'];
    }


    public function get_name() {
        return 'countdown';
    }

    public function get_title() {
        return __('Countdown', 'elementor_addon');
    }

    public function get_icon() {
        return 'eicon-time-line';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() { 
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Impostazioni', 'elementor_addon'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'event_date',
            [
                'label' => __('Data Evento', 'elementor_addon'),
                'type' => \Elementor\Controls_Manager::DATE_TIME,
                'default' => date('Y-m-d H:i:s', strtotime('+1 month')),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {  
    $settings = $this->get_settings_for_display();
    $event_date = !empty($settings['event_date']) ? $settings['event_date'] : date('Y-m-d H:i:s', strtotime('+1 month')); ?>

    <div id="countdown" data-date="<?php echo esc_attr($event_date); ?>">
        <div class="countdown-container">
            <div class="countdown-item">
                <span id="days">00</span>
                <span class="label">GIORNI</span>
            </div>
            <div class="separator"></div>
            <div class="countdown-item">
                <span id="hours">00</span>
                <span class="label">ORE</span>
            </div>
            <div class="separator"></div>
            <div class="countdown-item">
                <span id="minutes">00</span>
                <span class="label">MINUTI</span>
            </div>
            <div class="separator"></div>
            <div class="countdown-item">
                <span id="seconds">00</span>
                <span class="label">SECONDI</span>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const second = 1000,
                minute = second * 60,
                hour = minute * 60,
                day = hour * 24;

            let countdownElement = document.getElementById("countdown");
            let eventDate = countdownElement.getAttribute("data-date");

            if (!eventDate) return;

            const countDown = new Date(eventDate).getTime();
            
            const updateCountdown = () => {
                const now = new Date().getTime(),
                    distance = countDown - now;

                if (distance < 0) {
                    countdownElement.innerHTML = "<h2>Tempo Scaduto!</h2>";
                    clearInterval(interval);
                    return;
                }

                document.getElementById("days").innerText = Math.floor(distance / day);
                document.getElementById("hours").innerText = String(Math.floor((distance % day) / hour)).padStart(2, "0");
                document.getElementById("minutes").innerText = String(Math.floor((distance % hour) / minute)).padStart(2, "0");
                document.getElementById("seconds").innerText = String(Math.floor((distance % minute) / second)).padStart(2, "0");
            };

            updateCountdown();
            let interval = setInterval(updateCountdown, 1000);
        })();
    </script>

    <?php
  }	
  
  protected function content_template() {}
}