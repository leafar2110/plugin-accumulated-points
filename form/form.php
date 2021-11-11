<?php
include(AP_POINTS_URL.'/mail/mail.php');

global $wpdb;
$id_user = get_current_user_id();
$tabla_user = "{$wpdb->prefix}users";

$query = "SELECT * FROM {$wpdb->prefix}users WHERE `ID`=$id_user";
$list_user = $wpdb->get_results($query,ARRAY_A);
$points_accum = $list_user[0]['user_points'];


$query_conf = "SELECT * FROM {$wpdb->prefix}ap_points_configuration";
$list_conf = $wpdb->get_results($query_conf,ARRAY_A);



// descontar puntos
if(isset($_POST['btncanjear'])){
    $id = stripslashes_deep($_POST['id']); 
    $points = stripslashes_deep($_POST['points']);
    
    // Variables del correo de usuario
    $destinatario_user = $list_user[0]['user_email'];
    $asunto_user = 'hola';
    $cuerpo_user = 'texto';
    $cabeceras_user = array('Content-Type: text/html; charset=UTF-8');

    //variables del correo administrador
    $destinatario_admin = $list_conf[0]['email'];
    $asunto_admin = 'admin';
    $cuerpo_admin = 'texto para el admin';
    $cabeceras_admin = array('Content-Type: text/html; charset=UTF-8');

    

    $total_point = $points_accum - $points;

    $wpdb->update($tabla_user, array('ID'=>$id, 'user_points'=>$total_point), array('ID'=>$id));
   
    ap_mail_user($destinatario_user, $asunto_user , $cuerpo_user, $cabeceras_user);
    ap_mail_user($destinatario_admin, $asunto_admin , $cuerpo_admin, $cabeceras_admin);

}else{
    $total_point = $list_user[0]['user_points'];
}
