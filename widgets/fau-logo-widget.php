<?php

class Walker_Logo_Menu extends Walker_Nav_Menu
{
	
	function __construct() {
	 	echo '<div class="row logos-menu">';
	}
	
	function __destruct() {
		echo '</div>';
	}
	
	function start_lvl( &$output, $depth = 0, $args = array() ) {

	}
	
	function end_lvl( &$output, $depth = 0, $args = array() ) {

	}
	
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		if($depth === 0)
		{
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			$class_names = $value = '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;
			$classes[] = 'span2';

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<div' . $id . $value . $class_names .'>';

			$atts = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
			$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
			$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

			$atts['class'] = 'logo-item';

			$post = get_post($item->object_id);

			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output = $args->before;

			$item_output .= '<a class="logo-item" href="'.get_field('protocol', $item->object_id).get_field('link', $item->object_id).'">';
			$item_output .= get_the_post_thumbnail($item->object_id, 'logo-thumb');
			$item_output .= '</a>';
			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
	
	function end_el(&$output, $item, $depth=0, $args=array()) {      
		if($depth === 0)
		{
			$output .= "</div>\n"; 
		}
    }  
    
}


class FAUMenuLogosWidget extends WP_Widget
{
	function FAUMenuLogosWidget()
	{
		$widget_ops = array('classname' => 'FAUMenuLogosWidget', 'description' => 'Logo-Leiste' );
		$this->WP_Widget('FAUMenuLogosWidget', 'Logo-Leiste', $widget_ops);
	}

	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'menu-slug' => '' ) );
		$slug = $instance['menu-slug'];
		
		$menus = get_terms('nav_menu');
		
		echo '<p>';
			echo '<label for="'.$this->get_field_id('menu-slug').'">Menü: ';
				echo '<select id="'.$this->get_field_id('menu-slug').'" name="'.$this->get_field_name('menu-slug').'">';
					foreach($menus as $item)
					{
						echo '<option value="'.$item->slug.'"';
							if($item->slug == attribute_escape($slug)) echo ' selected';
						echo '>'.$item->name.'</option>';
					}
				echo '</select>';
			echo '</label>';
		echo '</p>';

	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['menu-slug'] = $new_instance['menu-slug'];
		return $instance;
	}

	function widget($args, $instance)
	{
		extract($args, EXTR_SKIP);

		echo $before_widget;
		
		echo '<div class="container">';
			echo '<div class="logos-menu-nav">';
				echo '<a id="logos-menu-prev" href="#">Zurück</a>';
				echo '<a id="logos-menu-next" href="#">Weiter</a>';
			echo '</div>';
		echo '</div>';
		$slug = empty($instance['menu-slug']) ? ' ' : $instance['menu-slug'];

		if (!empty($slug))
		{
			wp_nav_menu( array( 'menu' => $slug, 'container' => false, 'items_wrap' => '%3$s', 'link_before' => '', 'link_after' => '', 'walker' => new Walker_Logo_Menu));
		}
		
		echo '<div class="container"><a id="logos-menu-playpause" href="#"><span class="play">Abspielen</span><span class="pause">Pause</span></a></div>';
		
		echo $after_widget;
	}
}


add_action( 'widgets_init', create_function('', 'return register_widget("FAUMenuLogosWidget");') );