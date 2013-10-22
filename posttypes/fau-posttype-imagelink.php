<?php

function imagelink_taxonomy() {
	register_taxonomy(
		'imagelinks_category',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		'imagelink',   		 //post type name
		array(
			'hierarchical' 		=> true,
			'label' 			=> 'Kategorien',  //Display name
			'query_var' 		=> true,
			'rewrite'			=> array(
					'slug' 			=> 'imagelink', // This controls the base slug that will display before each term
					'with_front' 	=> false // Don't display the category base before
					)
			)
		);
}
add_action( 'init', 'imagelink_taxonomy');

// Register Custom Post Type
function imagelink_post_type() {

	load_plugin_textdomain( 'imagelink', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/' ); 

	$labels = array(
		'name'                => _x( 'Imagelinks', 'Post Type General Name', 'imagelink' ),
		'singular_name'       => _x( 'Imagelink', 'Post Type Singular Name', 'imagelink' ),
		'menu_name'           => __( 'Imagelink', 'imagelink' ),
		'parent_item_colon'   => __( 'Parent Imagelink', 'imagelink' ),
		'all_items'           => __( 'All Imagelinks', 'imagelink' ),
		'view_item'           => __( 'View Imagelink', 'imagelink' ),
		'add_new_item'        => __( 'Add New Imagelink', 'imagelink' ),
		'add_new'             => __( 'New Imagelink', 'imagelink' ),
		'edit_item'           => __( 'Edit Imagelink', 'imagelink' ),
		'update_item'         => __( 'Update Imagelink', 'imagelink' ),
		'search_items'        => __( 'Search imagelinks', 'imagelink' ),
		'not_found'           => __( 'No Imagelinks found', 'imagelink' ),
		'not_found_in_trash'  => __( 'No Imagelinks found in Trash', 'imagelink' ),
	);
	$args = array(
		'label'               => __( 'imagelink', 'imagelink' ),
		'description'         => __( 'Imagelink information', 'imagelink' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'thumbnail' ),
		'taxonomies'          => array( 'imagelinks_category' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'query_var'           => 'imagelink',
		'rewrite'             => false,
		'capability_type'     => 'page',
	);
	register_post_type( 'imagelink', $args );

}

// Hook into the 'init' action
add_action( 'init', 'imagelink_post_type', 0 );


?>