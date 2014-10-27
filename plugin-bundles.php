<?php


/**
 * Plugin Name: Plugin Bundles
 * Plugin URI: http://raison.co/
 * Description: Bundles of Plugins
 * Author: raisonon
 * Author URI: http://www.raison.co/
 * Version: 1.0.1
 * License: GPLv2 or later
 * Text Domain: woocommerce-taxtog
 */
 
 
// set paths
   
define('BNDLS_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));
define('BNDLS_DIR', WP_PLUGIN_DIR . '/' . BNDLS_NAME);
define('BNDLS_URL', WP_PLUGIN_URL . '/' . BNDLS_NAME);

// set version from plugin version

function BNDLS_get_version() {
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}

// set BNDLS version

//define('BNDLS_VERSION_KEY', 'BNDLS_VERSION');
define('BNDLS_VERSION_NUM', BNDLS_get_version());
add_option('BNDLS_VERSION_KEY', 'BNDLS_VERSION_NUM');     




// Enqueue Scripts

 

function BNDLS_stylesheet() {
		wp_register_style( 'BNDLS-css', BNDLS_URL . '/assets/css/BNDLS.css', array(), BNDLS_VERSION_NUM, 'all' );
		wp_enqueue_style( 'BNDLS-css' );
	} // End woo_shortcode_stylesheet()


add_action( 'wp_enqueue_scripts', 'BNDLS_stylesheet', 25 );


function register_BNDLS_script() {
	wp_register_script('BNDLS-js', BNDLS_URL . '/assets/js/BNDLS.js', array('jquery'), BNDLS_VERSION_NUM, true);
}


function print_BNDLS_head() {
	wp_print_scripts('BNDLS-js');
}


add_action('init', 'register_BNDLS_script');
add_action('wp_head', 'print_BNDLS_head');







// includes

include_once('includes/init.php');
//include_once('includes/general.php');
//include_once('includes/widget.php');
//include_once('includes/geo.php');

 
 
 
 
 
 
 
 
 
 
 
 
 
?>