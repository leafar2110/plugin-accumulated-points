<?php
function ap_style() {
	wp_enqueue_style( 'main-css', plugin_dir_url( dirname( __FILE__) ) . 'assets/css/main.css' );
	wp_enqueue_style( 'bootstrap-css', plugin_dir_url( dirname( __FILE__) ) . 'assets/css/bootstrap.min.css' );
}
add_action( 'admin_enqueue_scripts', 'ap_style' );

function ap_scripts() {
	wp_enqueue_script( 'bootstrap-js', plugin_dir_url( dirname( __FILE__) ) . 'assets/js/bootstrap.min.js', array( 'jquery' ), '', true );;
	wp_enqueue_script( 'main-js', plugin_dir_url( dirname( __FILE__) ) . 'assets/js/main.js', array( 'jquery' ), '', true );	
}
add_action( 'admin_enqueue_scripts', 'ap_scripts');