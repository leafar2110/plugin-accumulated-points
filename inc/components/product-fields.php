<?php

function ap_activate_field_product(){
  global $wpdb;
  $tabla_campign = "{$wpdb->prefix}ap_points_campaign";
  $query_conf = "SELECT $tabla_campign.`status` FROM  $tabla_campign";
  $list_conf = $wpdb->get_results($query_conf,ARRAY_A);

  if(!empty($list_conf) and $list_conf[0]['status']){
    add_action('woocommerce_product_options_general_product_data', 'ap_woocommerce_product_custom_fields');
    add_action('woocommerce_process_product_meta', 'ap_woocommerce_product_custom_fields_save');
  }

}

add_action('init', 'ap_activate_field_product');

function ap_woocommerce_product_custom_fields()
{

    global $woocommerce, $post;
    echo '<div class="product_custom_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => '_ap_custom_product_points_field',
            'placeholder' => 'Ingrese puntos',
            'label' => __('Puntos', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );
      woocommerce_wp_text_input(
        array(
          'id' => '_ap_custom_product_euro_field',
          'placeholder' => 'Ingrese euros',
          'label' => __('Euros', 'woocommerce'),
          'desc_tip' => 'true'
      )
    );
      
    echo '</div>';

    
}


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

?>