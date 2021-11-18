<?php
function ap_style() {
	wp_enqueue_style( 'main-css', plugin_dir_url( dirname( __FILE__) ) . 'assets/css/main.css' );

	wp_enqueue_style(
		'jquery-auto-complete',
		'https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.css',
		array(),
		'1.0.7'
	);


}
add_action( 'admin_enqueue_scripts', 'ap_style' );

function ap_scripts() {
	// wp_enqueue_script(
	// 	'jquery-auto-complete',
	// 	'https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js',
	// 	array( 'jquery' ),
	// 	'1.0.7',
	// 	true
	// );
	wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
	wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );
	
	wp_enqueue_script(
		'main-js',
		plugin_dir_url( dirname( __FILE__) ) . '/assets/js/main.js',
		array( 'jquery' ),
		'1.0.0',
		true
	);
	wp_localize_script(
		'main-js',
		'global',
		array(
			'ajax' => admin_url( 'admin-ajax.php' ),
		)
	);

	
	
}
add_action( 'admin_enqueue_scripts', 'ap_scripts');