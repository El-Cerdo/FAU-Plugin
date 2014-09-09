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
		'menu_name'           => __( 'Ads', 'ad' ),
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
		'capability_type'     => 'ad',
		'capabilities' => array(
            'edit_post' => 'edit_ad',
            'read_post' => 'read_ad',
            'delete_post' => 'delete_ad',
            'edit_posts' => 'edit_ads',
            'edit_others_posts' => 'edit_others_ads',
            'publish_posts' => 'publish_ads',
            'read_private_posts' => 'read_private_ads',
            'delete_posts' => 'delete_ads',
            'delete_private_posts' => 'delete_private_ads',
            'delete_published_posts' => 'delete_published_ads',
            'delete_others_posts' => 'delete_others_ads',
            'edit_private_posts' => 'edit_private_ads',
            'edit_published_posts' => 'edit_published_ads'
		),
		'map_meta_cap' => true
	);
	register_post_type( 'ad', $args );

}

// Hook into the 'init' action
add_action( 'init', 'ad_post_type', 0 );


function ad_restrict_manage_posts() {
	global $typenow;

	if( $typenow == "ad" ){
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
add_action( 'restrict_manage_posts', 'ad_restrict_manage_posts' );


function ad_post_types_admin_order( $wp_query ) {
	if (is_admin()) {

		$post_type = $wp_query->query['post_type'];

		if ( $post_type == 'ad') {

			if( ! isset($wp_query->query['orderby']))
			{
				$wp_query->set('orderby', 'title');
				$wp_query->set('order', 'ASC');
			}

		}
	}
}
add_filter('pre_get_posts', 'ad_post_types_admin_order');
