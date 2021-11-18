<?php 

// acumular puntos por categoria 

add_action( 'woocommerce_thankyou', function($order_id){
    global $wpdb;
    global $woocommerce;
  
    $tabla = "{$wpdb->prefix}ap_points_history";
    $tabla_config = "{$wpdb->prefix}ap_points_configuration";
    $tabla_campign = "{$wpdb->prefix}ap_points_campaign";
  
    $query_conf = "SELECT * FROM  $tabla_campign";
    $list_conf = $wpdb->get_results($query_conf,ARRAY_A);
    
    if(!empty($list_conf) and $list_conf[0]['status']){
      
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
  



function ap_activate_message(){
  global $wpdb;
  $tabla_campign = "{$wpdb->prefix}ap_points_campaign";
  $query_conf = "SELECT $tabla_campign.`status` FROM  $tabla_campign";
  $list_conf = $wpdb->get_results($query_conf,ARRAY_A);

  if(!empty($list_conf) and $list_conf[0]['status']){
    add_action( 'woocommerce_before_checkout_form', 'ap_message_checkout', 11 );
    add_action( 'woocommerce_before_single_product_summary', 'ap_message_cart', 11 );
  }

}

add_action('init', 'ap_activate_message');
  






function ap_message_checkout() {
    global $wpdb;
    global $product;
    $tabla_config = "{$wpdb->prefix}ap_points_configuration";
    $tabla_campign = "{$wpdb->prefix}ap_points_campaign";
    $query_notice = "SELECT $tabla_config.message_checkout_confign FROM  $tabla_config";
    $list_notice = $wpdb->get_results($query_notice,ARRAY_A);

    if(!empty($list_notice[0]['message_checkout_confign'])){
    $message = $list_notice[0]['message_checkout_confign'];
    $query_conf = "SELECT * FROM  $tabla_campign";
    $list_conf = $wpdb->get_results($query_conf,ARRAY_A);
    $cat_conf = json_decode($list_conf[0]['category_campaign']);
    $product_conf = json_decode($list_conf[0]['product_campaign']);
    $accumulated_point = 0;
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        
        $id = $cart_item["product_id"];
        $quantity = $cart_item["quantity"];
        $price = $cart_item['data']->get_price();
        $point_field = get_post_meta($id, '_ap_custom_product_points_field', true);
        $euro_field =  get_post_meta($id, '_ap_custom_product_euro_field', true);
   
        $product_exist = in_array($id, $product_conf, $strict = false);
        
        $term_id =  wc_get_product_term_ids( $id, 'product_cat' );
        $terms = get_option( "taxonomy_$term_id[0]" );
        $points_cat_custom = $terms["custom_term_meta"];
        $euros_cat_custom = $terms["term_meta"];

        if( has_term($cat_conf, 'product_cat',  $id) and !$point_field or $product_exist and !$point_field) {
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
        
    }
    wc_print_notice( __( $message.'  <strong>'.$accumulated_point.' puntos </strong>', 'woocommerce' ), 'notice' );
    }
}

function ap_message_cart() {
    global $wpdb;
    global $product;


    $tabla_config = "{$wpdb->prefix}ap_points_configuration";
    $tabla_campign = "{$wpdb->prefix}ap_points_campaign";
    $query_notice = "SELECT $tabla_config.message_single_confign FROM  $tabla_config";
    $list_notice = $wpdb->get_results($query_notice,ARRAY_A);

    if(!empty($list_notice[0]['message_single_confign'])){
    $message = $list_notice[0]['message_single_confign'];
    $query_conf = "SELECT * FROM  $tabla_campign";
    $list_conf = $wpdb->get_results($query_conf,ARRAY_A);
    $id = $product->get_id();
    $point_field = get_post_meta($id, '_ap_custom_product_points_field', true);
    $euro_field =  get_post_meta($id, '_ap_custom_product_euro_field', true);
    $price = number_format($product->get_price());
    $quantity = 1;
    $points_conf = number_format($list_conf[0]['points']);
    $euros_conf = number_format($list_conf[0]['euros']);
    $cat_conf = json_decode($list_conf[0]['category_campaign']);
    $product_conf = json_decode($list_conf[0]['product_campaign']);
    $product_exist = in_array($id, $product_conf, $strict = false);
    $term_id =  wc_get_product_term_ids( $id, 'product_cat' );
    $terms = get_option( "taxonomy_$term_id[0]" );
    $points_cat_custom = $terms["custom_term_meta"];
    $euros_cat_custom = $terms["term_meta"];
  
    if( has_term($cat_conf, 'product_cat', $id) and !$point_field or $product_exist and !$point_field) {
        if($terms and !$product_exist){
            $_points = ap_calculate_points($price, $quantity, $euros_cat_custom, $points_cat_custom);

        }else{
           $_points = ap_calculate_points($price, $quantity, $euros_conf, $points_conf);
        }
      }elseif($point_field){
         $_points = ap_calculate_points($price, $quantity, $euro_field, $point_field);

      }elseif($terms and !$point_field ){
         $_points = ap_calculate_points($price, $quantity, $euros_cat_custom, $points_cat_custom);

      }elseif($product_exist and !$point_field){
         $_points = ap_calculate_points($price, $quantity, $euros_conf, $points_conf);

      }
   
    
    wc_print_notice( __( $message.'  <strong>'.$_points.' puntos </strong>', 'woocommerce' ), 'notice' );
}
}