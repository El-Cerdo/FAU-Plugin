<?php

function synonyms_taxonomy() {
	register_taxonomy(
		'synonyms_category',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		'synonym',   		 //post type name
		array(
			'hierarchical' 		=> true,
			'label' 			=> 'Synonym-Kategorien',  //Display name
			'query_var' 		=> true,
			'rewrite'			=> array(
					'slug' 			=> 'synonym', // This controls the base slug that will display before each term
					'with_front' 	=> false // Don't display the category base before
					)
			)
		);
}
add_action( 'init', 'synonyms_taxonomy');


// Register Custom Post Type
function synonym_post_type() {	

	load_plugin_textdomain( 'synonym', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/' ); 
	
	$labels = array(
		'name'                => _x( 'Synonyms', 'Post Type General Name', 'synonym' ),
		'singular_name'       => _x( 'Synonym', 'Post Type Singular Name', 'synonym' ),
		'menu_name'           => __( 'Synonyms', 'synonym' ),
		'parent_item_colon'   => __( 'Parent Synonym', 'synonym' ),
		'all_items'           => __( 'All Synonyms', 'synonym' ),
		'view_item'           => __( 'View Synonym', 'synonym' ),
		'add_new_item'        => __( 'Add New Synonym', 'synonym' ),
		'add_new'             => __( 'New Synonym', 'synonym' ),
		'edit_item'           => __( 'Edit Synonym', 'synonym' ),
		'update_item'         => __( 'Update Synonym', 'synonym' ),
		'search_items'        => __( 'Search synonyms', 'synonym' ),
		'not_found'           => __( 'No synonyms found', 'synonym' ),
		'not_found_in_trash'  => __( 'No synonyms found in Trash', 'synonym' ),
	);
	$rewrite = array(
		'slug'                => 'synonym',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true,
	);
	$args = array(
		'label'               => __( 'synonym', 'synonym' ),
		'description'         => __( 'Synonym information', 'synonym' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'thumbnail' ),
		'taxonomies'          => array( 'synonyms_category' ),
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
		'query_var'           => 'synonym',
		'rewrite'             => $rewrite,
		'capability_type'     => 'synonym',
	);
	register_post_type( 'synonym', $args );

}

// Hook into the 'init' action
add_action( 'init', 'synonym_post_type', 0 );


function synonym_restrict_manage_posts() {
	global $typenow;

	if( $typenow == "synonym" ){
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
add_action( 'restrict_manage_posts', 'synonym_restrict_manage_posts' );


?>