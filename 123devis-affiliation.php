<?php
/*
Plugin Name: 123devis-affiliation
Plugin URI: http://wordpress.org/extend/plugins/123devis-affiliation/
Description: Le plugin 123devis-affiliation une solution simple, rapide et customisable pour intégrer l'ensemble des formulaires 123devis.com à votre site.
Version: 1.1.3
Author: Servicemagic EU
*/

require 'sm_functions.php';
require 'sm_wp_log.php';

include 'activation.php';

register_activation_hook(__FILE__,'sm_activation');
register_deactivation_hook(__FILE__,'sm_deactivation');

add_action( 'plugins_loaded', 'sm_plugins_loaded', 20 );

if (defined('SM_ENVTYPE')){
	foreach(array('gclid','iv_') as $fld){
		if (isset($_REQUEST[$fld])) {
			$_SESSION['track_sem_field_' . $fld] = $_REQUEST[$fld];
		}
	}
}

if ( !function_exists('sm_plugins_loaded') ) {
	function sm_plugins_loaded() {
		require "sm/autoloader.php";
		$sm_al_paths = apply_filters( "sm_autoload_paths", array(dirname(__FILE__)));
		sm_autoloader::attach( $sm_al_paths );

		//needed in admin and in shortcodes
		add_action('init', 'sm_init');

		//loading translations files
		$locale = apply_filters( 'plugin_locale', get_locale(), 'sm_translate');
		$mofile = dirname( __FILE__ ) . '/languages/sm_translate-' . $locale . '.mo';
		load_textdomain( 'sm_translate', $mofile );

		include 'administration.php';
		add_action('admin_menu', 'sm_admin_menu_setup');

		include 'shortcodes.php';
		add_shortcode( 'sm', 'sm_shortcode_func' );

		include 'admin_ajax.php';
		//admin only
		add_action( 'wp_ajax_sm_ajax_activities', 'sm_ajax_activities' );
		add_action( 'wp_ajax_sm_ajax_activity_search', 'sm_ajax_activity_search' );
		add_action( 'wp_ajax_sm_ajax_history_data', 'sm_ajax_history_data' );
		add_action( 'wp_ajax_sm_ajax_interview', 'sm_ajax_interview' );
		add_action( 'wp_ajax_sm_ajax_api_clear_cache', 'sm_ajax_api_clear_cache' );
		// Affiliation API WP - add generic pages
		add_action( 'wp_ajax_sm_ajax_create_genpage', 'sm_ajax_create_genpage' );

		//open
		add_action( 'wp_ajax_nopriv_sm_ajax_activity_search', 'sm_ajax_activity_search' );
		add_action( 'wp_ajax_sm_ajax_activity_search', 'sm_ajax_activity_search' );
		add_action( 'wp_ajax_nopriv_sm_ajax_sr_submit', 'sm_ajax_submit' );
		add_action( 'wp_ajax_sm_ajax_sr_submit', 'sm_ajax_submit' );
		add_action( 'wp_ajax_nopriv_sm_ajax_sp_submit', 'sm_ajax_submit' );
		add_action( 'wp_ajax_sm_ajax_sp_submit', 'sm_ajax_submit' );

		//include 'widget.php';
		//add_action('widgets_init','sm_widgets_init');

	}
}
