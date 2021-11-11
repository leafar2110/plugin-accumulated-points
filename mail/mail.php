<?php
   
function ap_mail_user($destinatario, $asunto , $cuerpo, $cabeceras){
    $destinatario = $destinatario;
    $asunto = $asunto;
    $cuerpo= $cuerpo;
    $cabeceras= $cabeceras;

    wp_mail( $destinatario, $asunto , $cuerpo, $cabeceras);
}

function ap_mail_admin($destinatario, $asunto , $cuerpo, $cabeceras){
    $destinatario = $destinatario;
    $asunto = $asunto;
    $cuerpo= $cuerpo;
    $cabeceras = array('Content-Type: text/html; charset=UTF-8');

    wp_mail( $destinatario, $asunto , $cuerpo, $cabeceras);
        
}