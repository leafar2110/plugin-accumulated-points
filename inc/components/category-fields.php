<?php
// this will add the custom meta field to the add new term page
function ap_activate_field_category(){
    global $wpdb;
    $tabla_campign = "{$wpdb->prefix}ap_points_campaign";
    $query_conf = "SELECT $tabla_campign.`status` FROM  $tabla_campign";
    $list_conf = $wpdb->get_results($query_conf,ARRAY_A);
  
    if(!empty($list_conf) and $list_conf[0]['status']){
        add_action( 'product_cat_add_form_fields', 'custom_url_taxonomy_add_new_meta_field', 10, 2 );
        add_action( 'product_cat_edit_form_fields', 'custom_url_taxonomy_edit_meta_field', 10, 2 );

    }

}

add_action('init', 'ap_activate_field_category');

function custom_url_taxonomy_add_new_meta_field() {
   
  
      ?>
      <div class="form-field">
          <label for="term_meta[custom_term_meta]"><?php _e( 'Puntos', 'custom_url_category' ); ?></label>
          <input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="">
          <p class="description"><?php _e( 'Inserisci un custom url prodotto per la categoria','custom_url_category' ); ?></p>
      </div>
      <div class="form-field">
          <label for="term_meta[term_meta]"><?php _e( 'Euros', 'custom_url_category' ); ?></label>
          <input type="text" name="term_meta[term_meta]" id="term_meta[term_meta]" value="">
          <p class="description"><?php _e( 'Inserisci un custom url prodotto per la categoria','custom_url_category' ); ?></p>
      </div>
  <?php
   
  }
  
  
  // Edit term page
  function custom_url_taxonomy_edit_meta_field($term) {
      global $woocommerce, $post;
      // put the term ID into a variable
      $t_id = $term->term_id;
   
      // retrieve the existing value(s) for this meta field. This returns an array
      $term_meta = get_option( "taxonomy_$t_id" ); ?>
      <tr class="form-field">
      <th scope="row" valign="top"><label for="term_meta[custom_term_meta]"><?php _e( 'Puntos', 'custom_url_category' ); ?></label></th>
          <td>
              <input type="text" placeholder="Ingrese Puntos" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="<?php if(!empty($term_meta['custom_term_meta'])){ echo esc_attr( $term_meta['custom_term_meta'] );} ?>">
              
          </td>
      </tr>
    <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[custom_term_meta]"><?php _e( 'Euros', 'custom_url_category' ); ?></label></th>
          <td>
              <input type="text" placeholder="Ingrese Euros" name="term_meta[term_meta]" id="term_meta[term_meta]" value="<?php if(!empty($term_meta['term_meta'])){ echo esc_attr( $term_meta['term_meta'] );} ?>">
              
          </td>
      </tr>
  <?php
    
  }
 
  
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
  