<?php 
//update status campaign
add_action( 'wp_ajax_nopriv_ap_update_status', 'ap_update_status' );
add_action( 'wp_ajax_ap_update_status', 'ap_update_status' );

function ap_update_status() {    

  if(isset($_POST['status_check'])){
    global $wpdb;
    $tabla_campaign = "{$wpdb->prefix}ap_points_campaign";
    $id = $_POST['IdCampaign'];
    $status = $_POST['status_check'];
    
    $wpdb->update($tabla_campaign, array('status' => $status), array('IdCampaign'=>$id));
  
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

  
?>