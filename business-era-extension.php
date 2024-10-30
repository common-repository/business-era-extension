<?php
/*
 * Plugin Name: Business Era Extension
 * Version: 1.0.0
 * Plugin URI: http://wordpress.org/plugins/business-era-extension/
 * Description: Plugin to extend features of Business Era Theme. This plugin registers custom post types, widgets and custom fields for the Business Era theme.
 * Author: Manesh Timilsina
 * Author URI: http://manesh.com.np/
 * License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Text Domain: business-era-extension
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'BEE_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

/**
 * Set up and initialize
 */
class Business_Era_Extension {

	private static $instance;

	/**
	 * Actions setup
	 */
	public function __construct() {

		add_action( 'plugins_loaded', array( $this, 'business_era_extension_post_types' ), 3 );
		add_action( 'admin_notices', array( $this, 'business_era_extension_notice' ), 4 );

	}

	/**
	 * Include required post types
	 */
	function business_era_extension_post_types() {
		
		//For custom post type team
		require_once( BEE_DIR . 'team.php' );

		//For widget team
		require_once( BEE_DIR . 'team-widget.php' );

		//For custom post type testimonials
		require_once( BEE_DIR . 'testimonials.php' );

		//For widget testimonials
		require_once( BEE_DIR . 'testimonials-widget.php' );

		//For custom post type portfolio
		require_once( BEE_DIR . 'portfolio.php' );

		//For widget portfolio
		require_once( BEE_DIR . 'portfolio-widget.php' );

	}

	/**
	 * Dispaly notice if business era theme is not active
	 */
	function business_era_extension_notice() {

		$theme  = wp_get_theme();

		$parent = wp_get_theme()->parent();

		if ( ($theme != 'Business Era' ) && ($theme != 'Business Era Pro' ) && ($parent != 'Business Era') && ($parent != 'Business Era Pro') ) {

		    echo '<div class="error">';

		    echo 	'<p>' . __('This <strong>Business Era Extension</strong> plugin is aimed to be used with the <a href="http://wordpress.org/themes/business-era/" target="_blank">Business Era</a> theme</p>', 'business-era-extension');

		    echo '</div>';		

		}
	}

	

	/**
	 * Returns the instance.
	 */
	public static function get_instance() {

		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

function business_era_extension_main() {

		return Business_Era_Extension::get_instance();
}

add_action( 'plugins_loaded', 'business_era_extension_main', 1 );