<?php
add_action( 'admin_menu', 'ap_create_admin_menu');

function ap_create_admin_menu() {
	add_menu_page(
		'Campaña',
		'Campaña',
		'manage_options',
		AP_POINTS_URL.'/admin/campaign-points.php',
		null,
		
	  );

	add_submenu_page (
		AP_POINTS_URL.'/admin/campaign-points.php',
		'Ajustes', 
		'Ajustes', 
		'manage_options', 
		AP_POINTS_URL.'/admin/settings-points.php',
    );
	add_submenu_page (
		AP_POINTS_URL.'/admin/campaign-points.php',
		'Usuarios y puntos', 
		'Usuarios y puntos', 
		'manage_options', 
		AP_POINTS_URL.'/admin/users-points.php',
    );
	add_submenu_page (
		'',
		'Usuarios y puntos', 
		'Usuarios y puntos', 
		'manage_options', 
		AP_POINTS_URL.'/admin/history-points.php',
    );

}