<?php

class Walker_Subpages_Menu extends Walker_Nav_Menu
{
	private $level = 1;
	private $count = array();
	private $element;
	
	function __construct() {
	 	echo '<div class="row subpages-menu">';
	}
	
	function __destruct() {
		echo '</div>';
	}
	
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		parent::start_lvl($output, $depth, $args);
		$this->level++;
		
		$this->count[$this->level] = 0;
	}
	
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		parent::end_lvl($output, $depth, $args);
		$this->level--;
	}
	
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$this->count[$this->level]++;
		
		if($this->level == 1) $this->element = $item;
		
		// Only show elements on the first level and only five on the second level
		if($this->level == 1 || ($this->level == 2 && $this->count[$this->level] <= 4))
		{
			$class_names = $value = '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;
			if($this->level == 1) $classes[] = 'span3';

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			if($this->level == 1) $output .= $indent . '<div' . $id . $value . $class_names .'>';
			else $output .= '<li>';

			$atts = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
			$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
			$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

			if($this->level == 1) $atts['class'] = 'subpage-item';

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
			if($post->post_type == 'imagelink')
			{
				$item_output .= '<a href="'.get_field('protocol', $item->object_id).get_field('link', $item->object_id).'">';
			}
			else
			{
				$item_output .= '<a'. $attributes .'>';
			}

			if($this->level == 1)
			{
				$item_output .= get_the_post_thumbnail($item->object_id, array(300,150));
				$item_output .= $args->link_before.'<h3>'.apply_filters( 'the_title', $item->title, $item->ID ) .'</h3>'. $args->link_after;
			}
			else
			{
				$item_output .= $args->link_before.apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			}

			$item_output .= '</a>';
			$item_output .= $args->after;
		}

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
	
	function end_el(&$output, $item, $depth=0, $args=array()) {      
		if($this->level == 1 || ($this->level == 2 && $this->count[$this->level] <= 4))
		{
			if($this->level == 1) $output .= "</div>\n";  
			else $output .= "</li>\n";
		}
		elseif($this->level == 2 && $this->count[$this->level] == 5)
		{
			$output .= '<li><a href="'.$this->element->url.'">Mehr …</a></li>';
		}
		
		if($this->level == 1)
		{
			if($this->count[$this->level] % 4 == 0) $output .= '</div><div class="row">';
		}
    }  
    
}


class FAUMenuSubpagesWidget extends WP_Widget
{
	function FAUMenuSubpagesWidget()
	{
		$widget_ops = array('classname' => 'FAUMenuSubpagesWidget', 'description' => 'Bebildertes Menü der Unterseiten' );
		$this->WP_Widget('FAUMenuSubpagesWidget', 'Portal-Menü', $widget_ops);
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
		$slug = empty($instance['menu-slug']) ? ' ' : $instance['menu-slug'];

		if (!empty($slug))
		{
			wp_nav_menu( array( 'menu' => $slug, 'container' => false, 'items_wrap' => '%3$s', 'link_before' => '', 'link_after' => '', 'walker' => new Walker_Subpages_Menu));
		}
		
		echo $after_widget;
	}
}


add_action( 'widgets_init', create_function('', 'return register_widget("FAUMenuSubpagesWidget");') );