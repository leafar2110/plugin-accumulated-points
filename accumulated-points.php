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



function ap_activate_plugin() {
  global $wpdb;
  $sql = " CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ap_points_configuration ( 
    `IdConfig` INT NOT NULL AUTO_INCREMENT, 
    `page` VARCHAR(100) NULL DEFAULT NULL, 
    `category` VARCHAR(256) NULL DEFAULT NULL, 
    `email` VARCHAR(64) NULL DEFAULT NULL, 
    `points` INT NULL DEFAULT '0', 
    `euros` INT NULL DEFAULT '0', 
    `fecha_inicio` DATE NULL DEFAULT NULL, 
    `fecha_final` DATE NULL DEFAULT NULL, 
    `stado` BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (`IdConfig`));";
  

  $sql1 = 'ALTER TABLE '.$wpdb->prefix.'users ADD user_points VARCHAR(64) NOT NULL;';
  $wpdb->get_results($sql);
  $wpdb->get_results($sql1);
}
register_activation_hook( __FILE__, 'ap_activate_plugin' );

function ap_deactivate_plugin() {
  global $wpdb;
  $sql = 'ALTER TABLE '.$wpdb->prefix.'users DROP user_points;';
  $wpdb->get_results($sql);
}
register_deactivation_hook( __FILE__, 'ap_deactivate_plugin' );
  
include (AP_POINTS_URL.'/inc/options.php');
include (AP_POINTS_URL.'/inc/post-type.php');
include (AP_POINTS_URL.'/inc/enqueue.php');

// acumular puntos por compras 

add_action( 'woocommerce_thankyou', function($order_id){

  global $wpdb;
  global $woocommerce;
  $order = new WC_Order($order_id);
  $id = $order->get_user_id();

  $table_user = $wpdb->prefix."users";
  $query = "SELECT * FROM {$wpdb->prefix}users  WHERE `ID`=$id";
  $list_user = $wpdb->get_results($query,ARRAY_A);
  if(empty($list_user[0]['user_points'])){
    $points_accum = 0;
  }else{
    $points_accum = $list_user[0]['user_points'];
  }
  $query_conf = "SELECT * FROM {$wpdb->prefix}ap_points_configuration";
  $list_conf = $wpdb->get_results($query_conf,ARRAY_A);
  if(empty($list_conf)){
    $list_conf = array();
  }
  $points_conf = number_format($list_conf[0]['points']);
  $euros_conf = number_format($list_conf[0]['euros']);
  $cat_conf = json_decode($list_conf[0]['category']);
  $accumulated_point = 0;

  if ( $order->get_status() != 'failed' ) {
    foreach ($order->get_items() as $item_key => $item ):
      $point_field = get_post_meta($item->get_product_id(), '_ap_custom_product_points_field', true);
      $euro_field =  get_post_meta($item->get_product_id(), '_ap_custom_product_euro_field', true);

      if( has_term($cat_conf, 'product_cat', $item->get_product_id()) and !$point_field) {
        // Get an instance of the WC_Product Object
        $_product = $item->get_product();
        $price_product = $_product->get_price();
        $quantity_product = $item->get_quantity();
        $amount = $price_product * $quantity_product;
        $points = ($amount / $euros_conf) * $points_conf;
        $accumulated_point = $accumulated_point + $points;
        
      }elseif($point_field){
        // Get an instance of the WC_Product Object
        $_product = $item->get_product();
        $price_product = $_product->get_price();
        $quantity_product = $item->get_quantity();
        $amount = $price_product * $quantity_product;
        $points = ($amount / $euro_field) * $point_field;
        $accumulated_point = $accumulated_point + $points;
      }

    endforeach;

    if($accumulated_point > 0){
      $total_point = $accumulated_point + $points_accum;
      $wpdb->update($table_user, array('ID'=>$id, 'user_points'=>$total_point), array('ID'=>$id));
    }
    
  }
});

//crear shortcode de template regalos 

function include_file($atts) {

  $atts = shortcode_atts(
      array(
      'path' => 'NULL',
      ), $atts, 'include' );
  
      ob_start();
      include_once('template/front/template-regalos.php');
      return ob_get_clean();
  
  }
  
  add_shortcode('include', 'include_file');

  

// mostrar regalos en la page seleccionada 

function ap_check_confirm_url() {
  global $wpdb;
  $query_conf = "SELECT * FROM {$wpdb->prefix}ap_points_configuration";
  $list_conf = $wpdb->get_results($query_conf,ARRAY_A);
  if(empty($list_conf)){
    $list_conf = array();
    $page = array();
  }else{
    $page = $list_conf[0]['page'];
    return false !== strpos( $_SERVER[ 'REQUEST_URI' ], '/'.$page.'/' );
  }
 
}

function ap_check_url() {
  if( ap_check_confirm_url() ) {
    add_filter( 'the_posts', 'ap_confirm_page' );
  }
}

function ap_confirm_page() {
  $posts = null;
  $post = new stdClass();
  $post->post_content = '[include]';
  $post->post_status = 'publish';
  $post->comment_status = 'closed';
  $post->ping_status = 'closed';
  $post->post_type = 'page';
  $post->filter = 'raw'; // important!
  $posts[] = $post;
  return $posts;
}

add_action( 'init', 'ap_check_url' );


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
    $page = $list_conf[0]['page'];
    $url = home_url($page);
  }
  return $url;
}


add_action('woocommerce_product_options_general_product_data', 'ap_woocommerce_product_custom_fields');

function ap_woocommerce_product_custom_fields()
{
    global $woocommerce, $post;
    echo '<div class="product_custom_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => '_ap_custom_product_points_field',
            'placeholder' => '',
            'label' => __('Puntos', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );
      woocommerce_wp_text_input(
        array(
          'id' => '_ap_custom_product_euro_field',
          'placeholder' => '',
          'label' => __('Euros', 'woocommerce'),
          'desc_tip' => 'true'
      )
    );
      
    echo '</div>';

}


add_action('woocommerce_process_product_meta', 'ap_woocommerce_product_custom_fields_save');

function ap_woocommerce_product_custom_fields_save($post_id)
{
 
    $woocommerce_custom_product_points_field = $_POST['_ap_custom_product_points_field'];
    if(isset($woocommerce_custom_product_points_field)){
    	update_post_meta($post_id, '_ap_custom_product_points_field', esc_attr($woocommerce_custom_product_points_field));
	}
  $woocommerce_custom_product_euro_field = $_POST['_ap_custom_product_euro_field'];
  if(isset($woocommerce_custom_product_euro_field)){
    update_post_meta($post_id, '_ap_custom_product_euro_field', esc_attr($woocommerce_custom_product_euro_field));
}
 
}
