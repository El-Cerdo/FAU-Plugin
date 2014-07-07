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
		'capability_type'     => 'glossary',
		'capabilities' => array(
			'edit_post' => 'edit_glossary',
			'edit_posts' => 'edit_glossary_items',
			'read_post' => 'read_glossary',
			'delete_post' => 'delete_glossary',
			'edit_others_posts' => 'edit_others_glossary',
			'publish_posts' => 'publish_glossary',
			'delete_posts' => 'delete_glossary',
		),
	);
	register_post_type( 'glossary', $args );

}

// Hook into the 'init' action
add_action( 'init', 'glossary_post_type', 0 );


function glossary_restrict_manage_posts() {
	global $typenow;

	if( $typenow == "glossary" ){
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
add_action( 'restrict_manage_posts', 'glossary_restrict_manage_posts' );


function glossary_post_types_admin_order( $wp_query ) {
	if (is_admin()) {

		$post_type = $wp_query->query['post_type'];

		if ( $post_type == 'glossary') {

			if( ! $wp_query->query['orderby'])
			{
				$wp_query->set('orderby', 'title');
				$wp_query->set('order', 'ASC');
			}

		}
	}
}
add_filter('pre_get_posts', 'glossary_post_types_admin_order');


?>