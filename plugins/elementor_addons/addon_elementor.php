<?php

/*

	Plugin Name: Ivrea Plugin

	Plugin URI: 

	Author:  Veronika Udod

	Version: 1.0.0

	Description: elementor plugin developed for addon.it.

	License: GNU General Public License v2 or later

	License URI: http://www.gnu.org/licenses/gpl-2.0.html


	Domain path: /languages/

*/

	if(!defined('ABSPATH')){

		die('Direct access not allowed');

	}

final class Elementor_Addon_Widgets {

	const VERSION = '1.0.0';

	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	const MINIMUM_PHP_VERSION = '7.0';

	private static $_instance = null;

	public static function instance() {

		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self();

		}

		return self::$_instance;

	}

	public function __construct() {

		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );

	}

	public function i18n() {

		load_plugin_textdomain( 'vinoplugin' );

	}


	public function on_plugins_loaded() {

		if ( $this->is_compatible() ) {

			add_action( 'elementor/init', [ $this, 'init' ] );

		}
		
	}
	
	public function is_compatible() {

		// Check if Elementor installed and activated

		if ( ! did_action( 'elementor/loaded' ) ) {

			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );

			return false;

		}

		// Check for required Elementor version

		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {

			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );

			return false;

		}

		// Check for required PHP version

		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {

			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );

			return false;

		}

		return true;

	}

	public function init() {

		$this->i18n();

		// Add Plugin actions

		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );

		add_action( 'elementor/controls/register', [ $this, 'init_controls' ] );

	}

	public function init_widgets( $widgets_manager ) {
	
		
		//HOMEPAGE
		require_once( __DIR__ . '/widgets/countdown/countdown.php' );
		require_once( __DIR__ . '/widgets/hero-text/hero-text.php' );
		require_once( __DIR__ . '/widgets/scroll-text/scroll-text.php' );
		require_once( __DIR__ . '/widgets/swiper-cards/swiper-cards.php' );
                require_once( __DIR__ . '/widgets/swiper-cards-grid/swiper-cards-grid.php' );
		require_once( __DIR__ . '/widgets/hover-text/hover-text.php' );
		require_once( __DIR__ . '/widgets/horizontal-cards/horizontal-cards.php' );
		require_once( __DIR__ . '/widgets/dot-button/dot-button.php' );
		require_once( __DIR__ . '/widgets/stats/stats.php' );
		require_once( __DIR__ . '/widgets/swiper-ospiti/swiper-ospiti.php' );
		require_once( __DIR__ . '/widgets/hero-dots/hero-dots.php' );
                require_once( __DIR__ . '/widgets/interactive-map/interactive-map.php' );
                require_once( __DIR__ . '/widgets/ivrea-accordion/ivrea-accordion.php' );
                require_once( __DIR__ . '/widgets/grid-ospiti/grid-ospiti.php' );
		
		//HOMEPAGE
		$widgets_manager->register( new \Elementor_Widget_Countdown() );
		$widgets_manager->register( new \Elementor_Widget_Hero_Text() );
		$widgets_manager->register( new \Elementor_Widget_Scroll_Text() );
		$widgets_manager->register( new \Elementor_Widget_Swiper_Cards() );
                $widgets_manager->register( new \Elementor_Widget_Swiper_Cards_Grid() );
		$widgets_manager->register( new \Elementor_Widget_Hover_Text() );
		$widgets_manager->register( new \Elementor_Widget_Horizontal_Cards() );
		$widgets_manager->register( new \Elementor_Widget_Dot_Button() );
		$widgets_manager->register( new \Elementor_Widget_Stats() );
		$widgets_manager->register( new \Elementor_Widget_Swiper_Ospiti() );
                $widgets_manager->register( new \Elementor_Widget_Hero_Dots() );
                $widgets_manager->register( new \Elementor_Widget_Interactive_Map() );
                $widgets_manager->register( new \Elementor_Widget_Ivrea_Accordion() );
                $widgets_manager->register( new \Elementor_Widget_Grid_Ospiti() );
		
	
	}	public function init_controls() {

		// Include Control files

		//require_once( __DIR__ . '/controls/test-control.php' );


		// Register control

		//\Elementor\Plugin::$instance->controls_manager->register_control( 'control-type-', new \Test_Control() );

	}

	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(

			/* translators: 1: Plugin name 2: Elementor */

			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'eleplugins' ),

			'<strong>' . esc_html__( 'Elementor Addon', 'eleplugins' ) . '</strong>',

			'<strong>' . esc_html__( 'Elementor', 'eleplugins' ) . '</strong>'

		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(

			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */

			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'eleplugins' ),

			'<strong>' . esc_html__( 'Elementor Addon', 'eleplugins' ) . '</strong>',

			'<strong>' . esc_html__( 'Elementor', 'eleplugins' ) . '</strong>',

			 self::MINIMUM_ELEMENTOR_VERSION

		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(

			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */

			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'eleplugins' ),

			'<strong>' . esc_html__( 'Ele Plugins', 'eleplugins' ) . '</strong>',

			'<strong>' . esc_html__( 'PHP', 'eleplugins' ) . '</strong>',

			 self::MINIMUM_PHP_VERSION

		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}
}
Elementor_Addon_Widgets::instance();

?>