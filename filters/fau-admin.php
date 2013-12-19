<?php


// Order posts by modified date

function set_post_order_in_admin( $wp_query ) {
  if ( is_admin() ) {
    $wp_query->set( 'orderby', 'modified' );
    $wp_query->set( 'order', 'DESC' );
  }
}
add_filter('pre_get_posts', 'set_post_order_in_admin' );

