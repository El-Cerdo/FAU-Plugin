<?php

function imagelink_taxonomy() {
	register_taxonomy(
		'imagelinks_category',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		'imagelink',   		 //post type name
		array(
			'hierarchical' 		=> true,
			'label' 			=> 'Bildlink-Kategorien',  //Display name
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
		'menu_name'           => __( 'Imagelinks', 'imagelink' ),
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
		'capability_type'     => 'imagelink',
		'capabilities' => array(
            'edit_post' => 'edit_imagelink',
            'read_post' => 'read_imagelink',
            'delete_post' => 'delete_imagelink',
            'edit_posts' => 'edit_imagelinks',
            'edit_others_posts' => 'edit_others_imagelinks',
            'publish_posts' => 'publish_imagelinks',
            'read_private_posts' => 'read_private_imagelinks',
            'delete_posts' => 'delete_imagelinks',
            'delete_private_posts' => 'delete_private_imagelinks',
            'delete_published_posts' => 'delete_published_imagelinks',
            'delete_others_posts' => 'delete_others_imagelinks',
            'edit_private_posts' => 'edit_private_imagelinks',
            'edit_published_posts' => 'edit_published_imagelinks'
		),
	);
	register_post_type( 'imagelink', $args );

}

// Hook into the 'init' action
add_action( 'init', 'imagelink_post_type', 0 );


function imagelink_restrict_manage_posts() {
	global $typenow;

	if( $typenow == "imagelink" ){
		$filters = get_object_taxonomies($typenow);
		
		foreach ($filters as $tax_slug) {
			$tax_obj = get_taxonomy($tax_slug);
			wp_dropdown_categories(array(
                'show_option_all' => sprintf('Alle %s anzeigen', $tax_obj->label),
                'taxonomy' => $tax_slug,
                'name' => $tax_obj->name,
                'orderby' => 'name',
                'selected' => isset($_GET[$tax_slug]) ? $_GET[$tax_slug] : '',
                'hierarchical' => $tax_obj->hierarchical,
                'show_count' => true,
                'hide_if_empty' => true
            ));
		}

	}
}
add_action( 'restrict_manage_posts', 'imagelink_restrict_manage_posts' );



function imagelink_post_types_admin_order( $wp_query ) {
	if (is_admin()) {

		$post_type = $wp_query->query['post_type'];

		if ( $post_type == 'imagelink') {

			if( ! isset($wp_query->query['orderby']))
			{
				$wp_query->set('orderby', 'title');
				$wp_query->set('order', 'ASC');
			}

		}
	}
}
add_filter('pre_get_posts', 'imagelink_post_types_admin_order');
