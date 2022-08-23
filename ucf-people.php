<?php
/*
Plugin Name: UCF People Custom Post Type
Description: Provides a people custom post type and related meta fields.
Version: 1.0.3.2
Author: UCF Web Communications / UCF COS Web
License: GPL3
Github Plugin URI: UCF/UCF-People-CPT
*/
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'UCF_PEOPLE__PLUGIN_URL', plugins_url( basename( dirname( __FILE__ ) ) ) );
define( 'UCF_PEOPLE__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'UCF_PEOPLE__STATIC_URL', UCF_PEOPLE__PLUGIN_URL . '/static' );
define( 'UCF_PEOPLE__PLUGIN_FILE', __FILE__ );

include_once 'includes/ucf-people-posttype.php';
include_once 'includes/ucf-people-shortcode.php';
include_once 'includes/ucf-people-group-taxonomy.php';

if ( ! function_exists( 'ucf_people_plugin_activation' ) ) {
	function ucf_people_plugin_activation() {
		UCF_People_PostType::register();
		UCF_People_Group_Taxonomy::register();
		flush_rewrite_rules();
		return;
	}

	register_activation_hook( UCF_PEOPLE__PLUGIN_FILE, 'ucf_people_plugin_activation' );
}

if ( ! function_exists( 'ucf_people_plugin_deactivation' ) ) {
	function ucf_people_plugin_deactivation() {
		flush_rewrite_rules();
		return;
	}

	register_deactivation_hook( UCF_PEOPLE__PLUGIN_FILE, 'ucf_people_plugin_deactivation' );
}

if ( ! function_exists( 'ucf_people_plugins_loaded' ) ) {
	function ucf_people_plugins_loaded() {
		add_action( 'init', array( 'UCF_People_PostType', 'register' ), 10, 0 );
		add_action( 'init', array( 'UCF_People_Group_Taxonomy', 'register' ), 10, 0 );

		// Add custom shortcode interface options
		if ( class_exists( 'WP_SCIF_Shortcode' ) ) {
			add_filter( 'wp_scif_add_shortcode', 'ucf_people_card_shortcode_interface', 10, 1 );
		}
	}

	add_action( 'plugins_loaded', 'ucf_people_plugins_loaded', 10, 0 );
}

if ( ! function_exists( 'ucf_people_enqueue_assets' ) ) {
	function ucf_people_enqueue_assets() {
		
		wp_enqueue_style( 'ucf_people_css', plugins_url( 'static/css/ucf-people.min.css', UCF_PEOPLE__PLUGIN_FILE ), false, false, 'all' );
		
	}

	add_action( 'wp_enqueue_scripts', 'ucf_people_enqueue_assets' );
}

?>
