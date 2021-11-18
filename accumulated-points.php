<?php
/**
 * Plugin Name: Accumulated points
 * Plugin URI: https://yquintero.xyz
 * Description: Accumulated points
 * Version: 1.0.0
 * Author: Yonnys Quintero
 * Author URI: https://yquintero.xyz
 * Text Domain: points
 * Domain Path: /i18n/languages/
 * Requires at least: 5.6
 * Requires PHP: 7.0
 *
 * @package points
 */

defined( 'ABSPATH' ) || exit;
define('AP_POINTS_URL',plugin_dir_path(__FILE__));

include (AP_POINTS_URL.'/inc/options.php');
include (AP_POINTS_URL.'/inc/enqueue.php');
include (AP_POINTS_URL.'/inc/components/form-ajax.php');
include (AP_POINTS_URL.'/inc/components/product-fields.php');
include (AP_POINTS_URL.'/inc/components/category-fields.php');
include (AP_POINTS_URL.'/inc/components/calculate-points.php');
// include (AP_POINTS_URL.'/inc/components/reset-point.php');

function ap_activate_plugin() {
  global $wpdb;
  $sql_configuration = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ap_points_configuration ( 
    `IdConfig` INT NOT NULL AUTO_INCREMENT, 
    `page_confign` VARCHAR(100) NULL DEFAULT NULL, 
    `status_order_confign` VARCHAR(256) NULL DEFAULT NULL, 
    `email_confign` VARCHAR(64) NULL DEFAULT NULL, 
    `message_single_confign` VARCHAR(150) NULL DEFAULT NULL, 
    `message_checkout_confign` VARCHAR(150) NULL DEFAULT NULL, 
    PRIMARY KEY (`IdConfig`));";
  $wpdb->get_results($sql_configuration);


  $sql_campaign  = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ap_points_campaign ( 
    `IdCampaign` INT NOT NULL AUTO_INCREMENT, 
    `title_campaign` VARCHAR(100) NULL DEFAULT NULL, 
    `category_campaign` VARCHAR(256) NULL DEFAULT NULL, 
    `product_campaign` VARCHAR(256) NULL DEFAULT NULL, 
    `points` INT NULL DEFAULT '0', 
    `euros` INT NULL DEFAULT '0', 
    `fecha_inicio` DATE NULL DEFAULT NULL, 
    `fecha_final` DATE NULL DEFAULT NULL, 
    `status` BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (`IdCampaign`));";
  $wpdb->get_results($sql_campaign);


  $sql_history = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ap_points_history (
    `IdHistory` int NOT NULL AUTO_INCREMENT,
    `ID` bigint unsigned NOT NULL,
    `point_history` INT NULL DEFAULT '0',
    `action_history` VARCHAR(64) NULL DEFAULT NULL,
    `status_history` int NOT NULL DEFAULT '1',
    `registered_history` datetime NOT NULL,
    PRIMARY KEY (`IdHistory`),
    KEY `ID` (`ID`),
    CONSTRAINT {$wpdb->prefix}ap_points_history_ibfk_1 FOREIGN KEY (`ID`) REFERENCES {$wpdb->prefix}users (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
   );";

  $wpdb->get_results($sql_history);

}
register_activation_hook( __FILE__, 'ap_activate_plugin' );

function ap_deactivate_plugin() {

}
register_deactivation_hook( __FILE__, 'ap_deactivate_plugin' );
  


add_filter ( 'woocommerce_account_menu_items', 'ap_more_link' );
function ap_more_link( $menu_links ){
  $new = array( 'iraregalos' => 'Regalos' );
        // Colocamos el nuevo elemento en la posición que nos interese (cambiando el 5 por el orden que queramos). 
  $menu_links = array_slice( $menu_links, 0, 5, true )
  + $new
  + array_slice( $menu_links, 5, NULL, true );
  return $menu_links;
}

add_filter( 'woocommerce_get_endpoint_url', 'ap_hook_endpoint', 10, 4 );
function ap_hook_endpoint( $url, $endpoint, $value, $permalink ){
  if( $endpoint === 'iraregalos' ) {
    // enlace donde queremos que apunte el menú
    global $wpdb;
    $query_conf = "SELECT * FROM {$wpdb->prefix}ap_points_configuration";
    $list_conf = $wpdb->get_results($query_conf,ARRAY_A);
    $page = $list_conf[0]['page_confign'];
    $url = home_url($page);
  }
  return $url;
}




