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

  <!-- LENIS -->
  <script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis"></script>

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
		  <img src="/wp-content/uploads/2025/12/logo.png" alt="Logo" class="logo">
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
	  </div>
	</header>


    <div class="cursor"></div>
    <div id="content" class="site-content">
