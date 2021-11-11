<?php
add_action( 'admin_menu', 'ap_create_admin_menu');

function ap_create_admin_menu() {
	add_menu_page(
		'Puntos',
		'Puntos',
		'manage_options',
		'puntos-regalo',
		null,
		'dashicons-tickets-alt'
	  );

	add_submenu_page (
		'puntos-regalo',
		'Ajustes', 
		'Ajustes', 
		'manage_options', 
		AP_POINTS_URL.'/admin/settings-points.php',
    );
	add_submenu_page (
		'puntos-regalo',
		'Usuarios y puntos', 
		'Usuarios y puntos', 
		'manage_options', 
		AP_POINTS_URL.'/admin/users-points.php',
    );

}