<?php

require_once 'vendor/autoload.php';

require_once 'includes/useful_functions.php';
require_once 'includes/custom_bones.php';
require_once 'includes/boostrap_gravity_form.php';

add_action('after_setup_theme', 'ci_setup_theme', 16);

function ci_setup_theme() {

	add_action('wp_enqueue_scripts', 'ci_assets');
	
	function ci_assets() {

		/*
		 * CSS
		 */
		$css =	array
			( 'main' => get_template_directory_uri() . '/css/main.css'
		);

		foreach ($css as $css_name => $css_path)
			wp_enqueue_style($css_name, $css_path);

		/*
		 * JS
		 */
		$js =	array
			( 'ci_lib'			=> array('url' => get_template_directory_uri() . '/js/lib.js', 'deps' => array('jquery'))
			, 'ci_main'		=> array('url' => get_template_directory_uri() . '/js/main.js', 'deps' => array('ci_lib'))
		);

		wp_enqueue_script('jquery');

		foreach ($js as $js_name => $js)
			wp_enqueue_script($js_name, $js['url'], $js['deps'], false, true);

		// JS IE in header
		$ie_script = get_template_directory_uri() . '/js/ie.min.js';
		add_action('wp_head', create_function('', 'echo \'<!--[if lt IE 9]><script type="text/javascript" src="'. $ie_script . '"></script><![endif]-->\';'));
	
	}

	add_filter('upload_mimes', 'allow_svg_mime_types');
	
	function allow_svg_mime_types( $mimes ){

		$mimes['svg'] = 'image/svg+xml';
		return $mimes;

	}

	/**
	 * I18n
	 */

	load_theme_textdomain('ci', get_template_directory() . '/lang');

	/**
	 * Thumbnails
	 */

	add_theme_support('post-thumbnails');

	add_image_size('fullscreen', 1920, 1080, true);

	/**
	 * Menus
	 */

	add_theme_support('menus');

	register_nav_menus(array(
		'main-nav' 		=> 'Navigation principale',
	));

	/**
	 * Custom header
	 */

	add_theme_support('custom-header', array(
		'width'                  => 0,
		'height'                 => 0,
		'flex-height'            => true,
		'flex-width'             => true,
		'header-text'            => false,
		'uploads'                => true,
	));
	
	/**
	 * Add class to body if mobile or tablet
	 */

	function ci_add_class_to_body($classes) {

		$detect = new Mobile_Detect;

		if($detect->isMobile() AND !$detect->isTablet())
			$classes[] = 'mobile';

		if($detect->isTablet())
			$classes[] = 'tablet';

		return $classes;

	}

	add_filter('body_class', 'ci_add_class_to_body');

	function ci_custom_excerpt_length($length) {
		return 35;
	}
	
	add_filter('excerpt_length', 'ci_custom_excerpt_length', 999);

}