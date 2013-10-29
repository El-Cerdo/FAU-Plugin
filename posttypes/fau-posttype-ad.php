<?php

function ads_taxonomy() {
	register_taxonomy(
		'ads_category',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		'ad',   		 //post type name
		array(
			'hierarchical' 		=> true,
			'label' 			=> 'Werbe-Kategorien',  //Display name
			'query_var' 		=> true,
			'rewrite'			=> array(
					'slug' 			=> 'ad', // This controls the base slug that will display before each term
					'with_front' 	=> false // Don't display the category base before
					)
			)
		);
}
add_action( 'init', 'ads_taxonomy');


// Register Custom Post Type
function ad_post_type() {

	load_plugin_textdomain( 'ad', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/' ); 
	
	$labels = array(
		'name'                => _x( 'Ads', 'Post Type General Name', 'ad' ),
		'singular_name'       => _x( 'Ad', 'Post Type Singular Name', 'ad' ),
		'menu_name'           => __( 'Ad', 'ad' ),
		'parent_item_colon'   => __( 'Parent Ad:', 'ad' ),
		'all_items'           => __( 'All Ads', 'ad' ),
		'view_item'           => __( 'View Ad', 'ad' ),
		'add_new_item'        => __( 'Add New Ad', 'ad' ),
		'add_new'             => __( 'New Ad', 'ad' ),
		'edit_item'           => __( 'Edit Ad', 'ad' ),
		'update_item'         => __( 'Update Ad', 'ad' ),
		'search_items'        => __( 'Search ads', 'ad' ),
		'not_found'           => __( 'No ads found', 'ad' ),
		'not_found_in_trash'  => __( 'No ads found in Trash', 'ad' ),
	);
	$rewrite = array(
		'slug'                => 'ad',
		'with_front'          => true,
		'pages'               => false,
		'feeds'               => false,
	);
	$args = array(
		'label'               => __( 'ad', 'ad' ),
		'description'         => __( 'Ad-Banners', 'ad' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'thumbnail', ),
		'taxonomies'          => array( 'ads_category' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'query_var'           => 'ad',
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
	);
	register_post_type( 'ad', $args );

}

// Hook into the 'init' action
add_action( 'init', 'ad_post_type', 0 );

?>