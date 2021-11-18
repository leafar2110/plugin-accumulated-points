<?php
add_action('init', 'a_register_cron');

function a_register_cron() {
  if( ! wp_next_scheduled( 'a_register_cron_campaign' ) ) {
      wp_schedule_event( current_time( 'timestamp' ), '5min', 'a_register_cron_campaign' );
  }
}

add_action( 'a_register_cron_campaign', 'a_rest_point_user' );

function a_rest_point_user() {
    global $wpdb;
    $tabla_campaign = "{$wpdb->prefix}ap_points_campaign";
    $table_user = "{$wpdb->prefix}ap_points_history";
    $table = "{$wpdb->prefix}users";
    $query_conf = "SELECT $tabla_campaign.fecha_final FROM $tabla_campaign";
    $list_conf = $wpdb->get_results($query_conf,ARRAY_A);
    $fin = $list_conf[0]['fecha_final'];
    $date  = strtotime(date("Y-m-d"));
    $findate =   strtotime($fin);
    if($date == $findate){

    $query = "SELECT $table.ID FROM $table";
    $list_user = $wpdb->get_results($query,ARRAY_A);
    foreach($list_user as $key => $user):
            $datos = [
                'IdHistory' => null,
                'ID' => $user['ID'],
                'point_history' => 0,
                'action_history' => 'Reinicio de puntos',
                'registered_history' => date("Y:m:d H:i:s"),
            ];
            $respuesta =  $wpdb->insert($table_user,$datos);
        endforeach;
    }
}


function my_cron_schedules($schedules){
    if(!isset($schedules["5min"])){
        $schedules["5min"] = array(
            'interval' => 86400,
            'display' => __('Once every 5 minutes'));
    }
    if(!isset($schedules["30min"])){
        $schedules["30min"] = array(
            'interval' => 30*60,
            'display' => __('Once every 30 minutes'));
    }
    return $schedules;
}
add_filter('cron_schedules','my_cron_schedules');