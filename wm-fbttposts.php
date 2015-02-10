<?php
/*
Plugin Name: WM FBTTPosts
Plugin URI: http://welisonmenezes.com.br/
Description: Auto posts in twitter and facebook
Version: 1.0
Author: Welison Menezes
Author URI: http://welisonmenezes.com.br/
License: GPLv2 or later
Text Domain: WM FBTTPosts
*/

//echo admin_url('options-general.php?page=wm-fbttposts-config');

/* ----------------------------------------
 * 	 GLOBALS AND CONFIGURATIONS
 * ----------------------------------------*/

session_start();
//session_unset($_SESSION);

/**
 * facebook dir
 */
define('FACEBOOK_SDK_V4_SRC_DIR', __DIR__ .'/libs/Facebook/src/Facebook/');

/**
 * url file FBTTPosts
 */
define( 'FBTTPOSTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * dir file FBTTPosts
 */
define( 'FBTTPOSTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * full url plugin
 */
define('FBTTPOSTS_FULL_URL', admin_url('admin.php?page=wm-fbttposts-connect'));

/* ----------------------------------------
 * 	 INCLUDES
 * ----------------------------------------*/

/*
 *	fbttposts classes
 */
require FBTTPOSTS_PLUGIN_DIR.'/class.FBTTPosts.php';
require FBTTPOSTS_PLUGIN_DIR.'/class.FBTTPostsUtil.php';
require FBTTPOSTS_PLUGIN_DIR.'/class.FBTTPostsShare.php';

/* ----------------------------------------
 * 	 INITIALIZATION
 * ----------------------------------------*/

FBTTPosts::fbttposts_load();
$fbShare = new FBTTPostsShare($fbConfig, $ttConfig);

function fbttposts_manage_files_scripts() 
{
	 wp_enqueue_script('jquery');
	 wp_enqueue_script('media-upload');
	 wp_enqueue_script('thickbox');
	 wp_register_script('my-upload', FBTTPOSTS_PLUGIN_URL.'views/js/fbttpostsManageFile.js', array('jquery','media-upload','thickbox'));
	 wp_enqueue_script('my-upload');
}

function fbttposts_manage_files_styles()
{
	wp_enqueue_style('thickbox');
}
add_action('admin_print_scripts', 'fbttposts_manage_files_scripts');
add_action('admin_print_styles', 'fbttposts_manage_files_styles');
?>