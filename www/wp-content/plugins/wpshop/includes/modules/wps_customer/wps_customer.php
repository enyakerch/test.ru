<?php
/**
 * Plugin Name: WPShop Customer
 * Plugin URI: http://www.wpshop.fr/documentations/presentation-wpshop/
 * Description: WpShop Customer Account
 * Version: 0.1
 * Author: Eoxia
 * Author URI: http://eoxia.com/
 */

/**
 * WpShop Customer Account bootstrap file
 * @author Jérôme ALLEGRE - Eoxia dev team <dev@eoxia.com>
 * @version 0.1
 * @package includes
 * @subpackage modules
 *
 */

if ( !defined( 'WPSHOP_VERSION' ) ) {
	die( __("You are not allowed to use this service.", 'wpshop') );
}

	/** Template Global vars **/
	DEFINE('WPS_ACCOUNT_DIR', basename(dirname(__FILE__)));
	DEFINE('WPS_ACCOUNT_PATH', str_replace( "\\", "/", str_replace( WPS_ACCOUNT_DIR, "", dirname( __FILE__ ) ) ) );
	DEFINE('WPS_ACCOUNT_URL', str_replace( str_replace( "\\", "/", ABSPATH), site_url() . '/', WPS_ACCOUNT_PATH ) );
	
	include( plugin_dir_path( __FILE__ ).'controller/wps_customer_ctr.php' );
	include( plugin_dir_path( __FILE__ ).'controller/wps_account_ctr.php' );
	include( plugin_dir_path( __FILE__ ).'controller/wps_account_dashboard_ctr.php' );
	include( plugin_dir_path( __FILE__ ).'model/wps_customer_mdl.php' );
	include( plugin_dir_path( __FILE__ ).'controller/wps_customer_group.php' );
	include( plugin_dir_path( __FILE__ ).'controller/customer_custom_list_table.class.php' );
	include( plugin_dir_path( __FILE__ ).'controller/wp_list_custom_groups.class.php' );
	include( plugin_dir_path( __FILE__ ).'controller/wp_list_custom_entities_customers.php' );
	
	$wps_customer = new wps_customer_ctr();
	$wps_account = new wps_account_ctr();
	$wps_account_dashboard = new wps_account_dashboard_ctr();
// 	$wps_customer_group = new wps_customer_group();
?>