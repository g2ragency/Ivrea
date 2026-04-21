<?php
/**
 * The header for our theme
 *
 * @package WP_Bootstrap_Starter
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="profile" href="http://gmpg.org/xfn/11">

  <!-- SWIPER -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <!-- GSAP -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

  <!-- SplitType -->
  <script src="https://unpkg.com/split-type"></script>

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

  <?php
  if (function_exists('wp_body_open')) {
    wp_body_open();
  } else {
    do_action('wp_body_open');
  }
  ?>

  <div id="page" class="site">
    <header class="site-header">
	  <div class="header-container">
		<div class="mobile-header-bar">
		  <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-link mobile-logo-link">
		    EX MACHINA
		  </a>
		  <button class="mobile-menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu-panel" aria-label="Apri menu">
		    <img class="icon-open" src="/wp-content/uploads/2026/04/hamburger-menu.svg" alt="Apri menu" />
		    <img class="icon-close" src="/wp-content/uploads/2026/04/menu-close.svg" alt="Chiudi menu" />
		  </button>
		</div>

		<!-- Menu a sinistra -->
		<div class="nav-left">
		  <?php
			wp_nav_menu(array(
			  'theme_location' => 'primary-left',
			  'menu_class'     => 'main-menu',
			  'container'      => false,
			));
		  ?>
		</div>

		<!-- Logo al centro -->
		<div class="logo-container">
		  <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-link">
		    EX MACHINA
		  </a>
		</div>

		<!-- Menu a destra -->
		<div class="nav-right">
		  <?php
			wp_nav_menu(array(
			  'theme_location' => 'primary-right',
			  'menu_class'     => 'main-menu',
			  'container'      => false,
			));
		  ?>
		</div>

		<div id="mobile-menu-panel" class="mobile-menu-panel" hidden>
		  <nav class="mobile-menu-main" aria-label="Menu mobile principale">
			<?php
			  wp_nav_menu(array(
				'theme_location' => 'primary-left',
				'menu_class'     => 'mobile-main-menu-list',
				'container'      => false,
			  ));
			  wp_nav_menu(array(
				'theme_location' => 'primary-right',
				'menu_class'     => 'mobile-main-menu-list',
				'container'      => false,
			  ));
			?>
		  </nav>

		  <div class="mobile-menu-footer-links">
			<a href="https://instagram.com" target="_blank" rel="noopener">Instagram</a>
			<span>|</span>
			<a href="https://facebook.com" target="_blank" rel="noopener">Facebook</a>
			<span>|</span>
			<a href="/privacy-policy">Privacy Policy</a>
		  </div>
		</div>
	  </div>
	</header>


    <div class="cursor"></div>
    <div id="content" class="site-content">
