<?php

// Register Custom Post Type
function ap_regalo() {

	$labels = array(
		'name'                  => _x( 'Regalo', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Regalos', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Regalos', 'text_domain' ),
		'name_admin_bar'        => __( 'Regalos', 'text_domain' ),
		'archives'              => __( 'Archivo', 'text_domain' ),
		'attributes'            => __( 'Atributos', 'text_domain' ),
		'parent_item_colon'     => __( 'Artículo principal', 'text_domain' ),
		'all_items'             => __( 'Regalos', 'text_domain' ),
		'add_new_item'          => __( 'Agregar ítem nuevo', 'text_domain' ),
		'add_new'               => __( 'Agregar nuevo', 'text_domain' ),
		'new_item'              => __( 'Nuevo artículo', 'text_domain' ),
		'edit_item'             => __( 'Editar artículo', 'text_domain' ),
		'update_item'           => __( 'Actualizar artículo', 'text_domain' ),
		'view_item'             => __( 'Ver ítem', 'text_domain' ),
		'view_items'            => __( 'Ver artículos', 'text_domain' ),
		'search_items'          => __( 'Artículo de búsqueda', 'text_domain' ),
		'not_found'             => __( 'No funciona', 'text_domain' ),
		'not_found_in_trash'    => __( 'No encontrado en la papelera', 'text_domain' ),
		'featured_image'        => __( 'Foto principal', 'text_domain' ),
		'set_featured_image'    => __( 'Establecer imagen destacada', 'text_domain' ),
		'remove_featured_image' => __( 'Eliminar imagen destacada', 'text_domain' ),
		'use_featured_image'    => __( 'Usar como imagen destacada', 'text_domain' ),
		'insert_into_item'      => __( 'Insertar en el artículo', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Subido a este artículo', 'text_domain' ),
		'items_list'            => __( 'Lista de artículos', 'text_domain' ),
		'items_list_navigation' => __( 'Navegación por la lista de elementos', 'text_domain' ),
		'filter_items_list'     => __( 'Lista de elementos de filtro', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Regalos', 'text_domain' ),
		'description'           => __( 'Regalos a canjear por puntos', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes', 'post-formats' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => 'puntos-regalo',
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-tickets-alt',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'ap_regalo', $args );

}
add_action( 'init', 'ap_regalo', 0 );