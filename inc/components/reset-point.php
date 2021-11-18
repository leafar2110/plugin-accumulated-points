<?php
    add_action('wp_ajax_ap_ajax_action_cron', 'ap_ajax_action_cron');
    add_action('wp_ajax_nopriv_ap_ajax_action_cron', 'ap_ajax_action_cron');
    function ap_ajax_action_cron() {
        $token_cron = $_GET['token'];
        $token_validate = "baDCSERVAxuyFRTYFVuy6rfqvuYTeDRFTYVBIURFVTR32";

        if($token_cron == $token_validate){
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
    
}