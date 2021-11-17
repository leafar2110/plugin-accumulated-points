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
    `point_history` int NOT NULL,
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
  

// acumular puntos por categoria 

add_action( 'woocommerce_thankyou', function($order_id){
  global $wpdb;
  global $woocommerce;

  $tabla = "{$wpdb->prefix}ap_points_history";
  $tabla_config = "{$wpdb->prefix}ap_points_configuration";
  $tabla_campign = "{$wpdb->prefix}ap_points_campaign";

  $query_conf = "SELECT * FROM  $tabla_campign";
  $list_conf = $wpdb->get_results($query_conf,ARRAY_A);
  
  if($list_conf[0]['status']){
    
    $order = new WC_Order($order_id);
    $id = $order->get_user_id();
    $query = "SELECT $tabla.point_history FROM $tabla WHERE ID = $id ORDER BY $tabla.registered_history DESC LIMIT 1";
    $list_user = $wpdb->get_results($query,ARRAY_A);

   
    if(empty($list_user[0]['point_history'])){
      $points_accum = 0;
    }else{
      $points_accum = $list_user[0]['point_history'];
    }

    $points_conf = number_format($list_conf[0]['points']);
    $euros_conf = number_format($list_conf[0]['euros']);
    $cat_conf = json_decode($list_conf[0]['category_campaign']);
    $product_conf = json_decode($list_conf[0]['product_campaign']);
    $accumulated_point = 0;
    $query_config = "SELECT $tabla_config.status_order_confign FROM $tabla_config LIMIT 1";
    $status_config = $wpdb->get_results($query_config,ARRAY_A);
    $status_order = 'wc-'.$order->get_status();
    var_dump($status_config);
    var_dump($status_order);
    $status_configuration = $status_config[0]['status_order_confign'];

    if ( $status_order == $status_configuration ) {

      foreach ($order->get_items() as $item_key => $item ):
        $point_field = get_post_meta($item->get_product_id(), '_ap_custom_product_points_field', true);
        $euro_field =  get_post_meta($item->get_product_id(), '_ap_custom_product_euro_field', true);
        $_product = $item->get_product();
        $price = $_product->get_price();
        $quantity = $item->get_quantity();
        $product_exist = in_array($item->get_product_id(), $product_conf, $strict = false);
        
        $term_id =  wc_get_product_term_ids( $item->get_product_id(), 'product_cat' );
        $terms = get_option( "taxonomy_$term_id[0]" );
        $points_cat_custom = $terms["custom_term_meta"];
        $euros_cat_custom = $terms["term_meta"];

      
        if( has_term($cat_conf, 'product_cat', $item->get_product_id()) and !$point_field or $product_exist and !$point_field) {
          if($terms and !$product_exist){
            $accumulated_point = $accumulated_point + ap_calculate_points($price, $quantity, $euros_cat_custom, $points_cat_custom);
          }else{
            $accumulated_point = $accumulated_point + ap_calculate_points($price, $quantity, $euros_conf, $points_conf);
          }
        }elseif($point_field){
          $accumulated_point = $accumulated_point + ap_calculate_points($price, $quantity, $euro_field, $point_field);
        }elseif($terms and !$point_field ){
          $accumulated_point = $accumulated_point + ap_calculate_points($price, $quantity, $euros_cat_custom, $points_cat_custom);
        }elseif($product_exist and !$point_field){
          $accumulated_point = $accumulated_point + ap_calculate_points($price, $quantity, $euros_conf, $points_conf);
        }

      endforeach;

      if($accumulated_point > 0){
        $total_point = $accumulated_point + $points_accum;
        
        $datos = [
            'IdHistory' => null,
            'ID' => $id,
            'point_history' => $total_point,
            'action_history' => '+'.$accumulated_point,
            'registered_history' => date("Y:m:d H:i:s"),
        ];
        $respuesta =  $wpdb->insert($tabla,$datos);

      }
      
    }
  }
});


function ap_calculate_points($price, $quantity, $euros, $puntos){
  $amount = $price * $quantity;
  $points = ($amount / $euros) * $puntos;
  return $points;
}


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


add_action('woocommerce_product_options_general_product_data', 'ap_woocommerce_product_custom_fields');

function ap_woocommerce_product_custom_fields()
{
  global $wpdb;
  $tabla_campign = "{$wpdb->prefix}ap_points_campaign";
  $query_conf = "SELECT $tabla_campign.`status` FROM  $tabla_campign";
  $list_conf = $wpdb->get_results($query_conf,ARRAY_A);

  if(!empty($list_conf) and $list_conf[0]['status']){
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


function ap_reset_points(){
  global $wpdb;
  $tabla = "{$wpdb->prefix}ap_points_configuration";
  $table_user = "{$wpdb->prefix}users";
  $query_conf = "SELECT * FROM $tabla";
  $list_conf = $wpdb->get_results($query_conf,ARRAY_A);
  $fin = $list_conf[0]['fecha_final'];
  $pdate  = strtotime(date("Y-m-d"));
  $mydate =   strtotime($fin);
  
      if ($pdate==$mydate){
        echo 'es igual a:'.$pdate;
        // $update = $wpdb->update($table_user, array('user_points'=>0), array('user_status_points' => 0));
      }else{
        echo 'no es igual'.$mydate;
      }
}





//Form product AJAX


function ja_ajax_search() {

	$results = new WP_Query( array(
		'post_type'     => array( 'product'),
		'post_status'   => 'publish',
		'nopaging'      => true,
		'posts_per_page'=> 100,
		's'             => stripslashes( $_GET['searchTerm'] ),
	) );

	$items = array();

	if ( !empty( $results->posts ) ) {
		foreach ( $results->posts as $result ) {
			$items[] = $result;
      ;
		}
	}

	wp_send_json_success( $items );
}
add_action( 'wp_ajax_search_site',        'ja_ajax_search' );
add_action( 'wp_ajax_nopriv_search_site', 'ja_ajax_search' );



// this will add the custom meta field to the add new term page

function custom_url_taxonomy_add_new_meta_field() {
  global $wpdb;
  $tabla_campign = "{$wpdb->prefix}ap_points_campaign";
  $query_conf = "SELECT $tabla_campign.`status` FROM  $tabla_campign";
  $list_conf = $wpdb->get_results($query_conf,ARRAY_A);

  if(!empty($list_conf) and $list_conf[0]['status']){

	?>
	<div class="form-field">
		<label for="term_meta[custom_term_meta]"><?php _e( 'Puntos', 'custom_url_category' ); ?></label>
		<input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="">
		<p class="description"><?php _e( 'Inserisci un custom url prodotto per la categoria','custom_url_category' ); ?></p>
    <label for="term_meta[term_meta]"><?php _e( 'Euros', 'custom_url_category' ); ?></label>
		<input type="text" name="term_meta[term_meta]" id="term_meta[term_meta]" value="">
		<p class="description"><?php _e( 'Inserisci un custom url prodotto per la categoria','custom_url_category' ); ?></p>
	</div>
<?php
  }
}
add_action( 'product_cat_add_form_fields', 'custom_url_taxonomy_add_new_meta_field', 10, 2 );

// Edit term page
function custom_url_taxonomy_edit_meta_field($term) {
  global $wpdb;
  $tabla_campign = "{$wpdb->prefix}ap_points_campaign";
  $query_conf = "SELECT $tabla_campign.`status` FROM  $tabla_campign";
  $list_conf = $wpdb->get_results($query_conf,ARRAY_A);

  if(!empty($list_conf) and $list_conf[0]['status']){
    global $woocommerce, $post;
	// put the term ID into a variable
	$t_id = $term->term_id;
 
	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$t_id" ); ?>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[custom_term_meta]"><?php _e( 'Puntos', 'custom_url_category' ); ?></label></th>
		<td>
			<input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="<?php echo esc_attr( $term_meta['custom_term_meta'] ) ? esc_attr( $term_meta['custom_term_meta'] ) : ''; ?>">
			<p class="description"><?php _e( 'Inserisci un custom url prodotto per la categoria','custom_url_category' ); ?></p>
		</td>
	</tr>
  <th scope="row" valign="top"><label for="term_meta[custom_term_meta]"><?php _e( 'Euros', 'custom_url_category' ); ?></label></th>
		<td>
			<input type="text" name="term_meta[term_meta]" id="term_meta[term_meta]" value="<?php echo esc_attr( $term_meta['term_meta'] ) ? esc_attr( $term_meta['term_meta'] ) : ''; ?>">
			<p class="description"><?php _e( 'Inserisci un custom url prodotto per la categoria','url_category' ); ?></p>
		</td>
	</tr>
<?php
  }
}
add_action( 'product_cat_edit_form_fields', 'custom_url_taxonomy_edit_meta_field', 10, 2 );

// Save extra taxonomy fields callback function.
function save_taxonomy_custom_meta( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}
}  
add_action( 'edited_product_cat', 'save_taxonomy_custom_meta', 10, 2 );  
add_action( 'create_product_cat', 'save_taxonomy_custom_meta', 10, 2 );

