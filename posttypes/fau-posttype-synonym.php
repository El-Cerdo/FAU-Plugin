<?php

function synonyms_taxonomy() {
	register_taxonomy(
		'synonyms_category',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		'synonym',   		 //post type name
		array(
			'hierarchical' 		=> true,
			'label' 			=> __('Synonym-Kategorien', 'fau'),  //Display name
			'query_var' 		=> true,
			'rewrite'			=> array(
					'slug' 			=> 'synonyms', // This controls the base slug that will display before each term
					'with_front' 	=> false // Don't display the category base before
					)
			)
		);
}
add_action( 'init', 'synonyms_taxonomy');


// Register Custom Post Type
function synonym_post_type() {	
	
	$labels = array(
		'name'                => _x( 'Synonyme', 'Post Type General Name', 'fau' ),
		'singular_name'       => _x( 'Synonym', 'Post Type Singular Name', 'fau' ),
		'menu_name'           => __( 'Synonyme', 'fau' ),
		'parent_item_colon'   => __( 'Übergeordnete Synonyme', 'fau' ),
		'all_items'           => __( 'Alle Synonyme', 'fau' ),
		'view_item'           => __( 'Synonyme ansehen', 'fau' ),
		'add_new_item'        => __( 'Synonym hinzufügen', 'fau' ),
		'add_new'             => __( 'Neues Synonym', 'fau' ),
		'edit_item'           => __( 'Synonym bearbeiten', 'fau' ),
		'update_item'         => __( 'Synonym aktualisieren', 'fau' ),
		'search_items'        => __( 'Synonym suchen', 'fau' ),
		'not_found'           => __( 'Keine Synonyme gefunden', 'fau' ),
		'not_found_in_trash'  => __( 'Keine Synonyme im Papierkorb gefunden', 'fau' ),
	);
	$rewrite = array(
		'slug'                => 'synonym',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true,
	);
	$args = array(
		'label'               => __( 'synonym', 'fau' ),
		'description'         => __( 'Synonym Informationen', 'fau' ),
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
		'capabilities' => array(
            'edit_post' => 'edit_synonym',
            'read_post' => 'read_synonym',
            'delete_post' => 'delete_synonym',
            'edit_posts' => 'edit_synonyms',
            'edit_others_posts' => 'edit_others_synonyms',
            'publish_posts' => 'publish_synonyms',
            'read_private_posts' => 'read_private_synonyms',
            'delete_posts' => 'delete_synonyms',
            'delete_private_posts' => 'delete_private_synonyms',
            'delete_published_posts' => 'delete_published_synonyms',
            'delete_others_posts' => 'delete_others_synonyms',
            'edit_private_posts' => 'edit_private_synonyms',
            'edit_published_posts' => 'edit_published_synonyms'
		),
		'map_meta_cap' => true
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
                'show_option_all' => sprintf(__('Alle %s anzeigen', 'fau'), $tax_obj->label),
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


function synonym_post_types_admin_order( $wp_query ) {
	if (is_admin()) {

		$post_type = $wp_query->query['post_type'];

		if ( $post_type == 'synonym') {

			if( ! isset($wp_query->query['orderby']))
			{
				$wp_query->set('orderby', 'title');
				$wp_query->set('order', 'ASC');
			}

		}
	}
}
add_filter('pre_get_posts', 'synonym_post_types_admin_order');
