<?php
/*
Plugin Name: PL Manager
Description: Simple Post Like Management System
Version: 1.0
Author: Manidip Mandal
License: GPLv2 or later for adding like and unlike functionality for posts


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

 */
?>
<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
global $wpdb;

define( 'MMPLM_WP_VERSION_REQUIRED', '4.0');
define( 'MMPLM_PHP_VERSION_REQUIRED', '5.6');
define( 'MMPLM_PLUGIN_NAME', 'mmplm');
define( 'MMPLM_PLUGIN_URL', plugin_dir_url(  __FILE__  ) );
define( 'MMPLM_PLUGIN_DIR', plugin_basename( __DIR__ ) );
define( 'MMPLM_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'MMPLM_CSS_DIR_URL', plugin_dir_url(  __FILE__).'css/' );
define( 'MMPLM_JS_DIR_URL', MMPLM_PLUGIN_URL.'js/' );
define( 'MMPLM_TABLE_NAME', $wpdb->prefix . "pl_management" );
define( 'MMPLM_PLUGIN_VERSION', '1.0' );

(!defined('WP_DEBUG'))?define('WP_DEBUG',false):'';
(!defined('WP_DEBUG_DISPLAY'))?define('WP_DEBUG_DISPLAY',false):'';

 /* Compare PHP Version */
    
if ( version_compare( PHP_VERSION, MMPLM_PHP_VERSION_REQUIRED, '<' ) ) {
 deactivate_plugins( basename( __FILE__ ) );
 wp_die(
 '<p>' .
 sprintf(
	 __( 'This plugin can not be activated because it requires a PHP version greater than %1$s. Your PHP version can be updated by your hosting company.', MMPLM_PLUGIN_NAME ),
	 MMPLM_PHP_VERSION_REQUIRED
 ). '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'Go Back', MMPLM_PLUGIN_NAME ) . '</a>'
 );
}

require_once(plugin_dir_path( __FILE__ ).'options.php');
require_once(plugin_dir_path( __FILE__ ).'functions.php');
require_once(plugin_dir_path( __FILE__ ).'admin.php');
require_once(plugin_dir_path( __FILE__ ).'user.php');
require_once(plugin_dir_path( __FILE__ ).'ajax.php');


define( 'MMPLM_CLIENT_IP_ADDRESS', MMPLM_Functions::mmplm_get_ip_address());


if(!class_exists('MMPostLikeManagement')){
    
    class MMPostLikeManagement{
        
        public static $instance = NULL;
        public static function get_instance(){
            if(NULL == self::$instance){
                self::$instance == new self;
            }
            return self::$instance;
        }
        
        public function __construct(){
	  
            add_action('init', array($this,'mmplm_load_plugin_textdomain'));
            add_filter('plugin_action_links', array($this,'mmplm_plugin_links'), 10, 2);
            register_activation_hook( __FILE__, array($this,'mmplm_activate'));
            register_deactivation_hook( __FILE__, array($this,'mmplm_deactivate'));
            register_uninstall_hook(__FILE__,'mmplm_uninstall');
	  
	  }
        
/*
*Load Plugin Text Domain
*/
        public function mmplm_load_plugin_textdomain() {
          load_plugin_textdomain(MMPLM_PLUGIN_NAME, false, plugin_basename( __DIR__ ) . '/languages');
        }

/*
 *Add Setting Link In Plugin
 */     
     public function mmplm_plugin_links($links, $file)
     {
	  if ($file == plugin_basename(__FILE__))
	  {
	       $settings_link = '<a href="' . admin_url('options-general.php?page=post-like-manager') . '">' . __('Settings', MMPLM_PLUGIN_NAME) . '</a>';
	       array_unshift($links, $settings_link);
	  }
	  return $links;
     }
     
 /*
 *Fires on plugin activation
 */
 
    public function mmplm_activate() {

	       global $wp_version,$wpdb;
    
			/*Create Table For Plugin*/
    
	      $charset_collate = $wpdb->get_charset_collate();
	      if ($wpdb->get_var("show tables like '".MMPLM_TABLE_NAME."'") != MMPLM_TABLE_NAME) { 
	       $sql = "CREATE TABLE " . MMPLM_TABLE_NAME . " (
				   `id` bigint(11) NOT NULL AUTO_INCREMENT,
				   `post_id` int(11) NOT NULL,
				   `value` int(2) NOT NULL,
				   `date_time` datetime NOT NULL,
				   `ip` varchar(40) NOT NULL,
				   `user_id` int(11) NOT NULL DEFAULT '0',
				   `last_action` varchar(40) NOT NULL,
				   PRIMARY KEY (`id`)
			   ) $charset_collate;";
	   
	      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	      dbDelta($sql);
	      }
	      
	      foreach(MMPLM_Options::$settings_group as $key=>$value)
			{
		     add_option($key, $value, '', 'yes');
			}
			//file_put_contents(__DIR__.'/pl_manager_loggg.txt', ob_get_contents());
	  }
      
            public function mmplm_deactivate(){
           
               //Nothing will happen  
                  
        } 
        
         public static function mmplm_uninstall(){
    
            global $wpdb;
            
           if(get_option('plm-remove-plugin-settings'))
           {
                 $wpdb->query("DROP TABLE IF EXISTS ".MMPLM_TABLE_NAME);
                 foreach(MMPLM_Options::$settings_group as $key=>$value)  { delete_option($key);}
           }
	 
        }
 
    }
    
}

$mm_post_like_management = MMPostLikeManagement::get_instance();
(is_admin())?MMPLM_Admin::get_instance():MMPLM_User::get_instance();
