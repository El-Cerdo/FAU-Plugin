<?php

function glossary_taxonomy() {
	register_taxonomy(
		'glossary_category',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		'glossary',   		 //post type name
		array(
			'hierarchical' 		=> true,
			'label' 			=> 'Glossar-Kategorien',  //Display name
			'query_var' 		=> true,
			'rewrite'			=> array(
					'slug' 			=> 'glossary', // This controls the base slug that will display before each term
					'with_front' 	=> false // Don't display the category base before
					)
			)
		);
}
add_action( 'init', 'glossary_taxonomy');


// Register Custom Post Type
function glossary_post_type() {	

	load_plugin_textdomain( 'glossary', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/' ); 
	
	$labels = array(
		'name'                => _x( 'Glossary', 'Post Type General Name', 'glossary' ),
		'singular_name'       => _x( 'Glossary item', 'Post Type Singular Name', 'glossary' ),
		'menu_name'           => __( 'Glossary', 'glossary' ),
		'parent_item_colon'   => __( 'Parent item', 'glossary' ),
		'all_items'           => __( 'All Glossary items', 'glossary' ),
		'view_item'           => __( 'View item', 'glossary' ),
		'add_new_item'        => __( 'Add New Glossary item', 'glossary' ),
		'add_new'             => __( 'New Glossary item', 'glossary' ),
		'edit_item'           => __( 'Edit item', 'glossary' ),
		'update_item'         => __( 'Update item', 'glossary' ),
		'search_items'        => __( 'Search Glossary items', 'glossary' ),
		'not_found'           => __( 'No Glossary items found', 'glossary' ),
		'not_found_in_trash'  => __( 'No Glossary items found in Trash', 'glossary' ),
	);
	$rewrite = array(
		'slug'                => 'glossary',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true,
	);
	$args = array(
		'label'               => __( 'glossary', 'glossary' ),
		'description'         => __( 'Glossary information', 'glossary' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'thumbnail' ),
		'taxonomies'          => array( 'glossary_category' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'query_var'           => 'glossary',
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
	);
	register_post_type( 'glossary', $args );

}

// Hook into the 'init' action
add_action( 'init', 'glossary_post_type', 0 );



?>