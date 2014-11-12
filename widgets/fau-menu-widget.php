<?php

class Walker_Subpages_Menu extends Walker_Nav_Menu
{
	private $level = 1;
	private $count = array();
	private $element;
	private $showdescription = FALSE;
	
	function __construct($showdescription) {
	 	echo '<ul class="row subpages-menu">';
		if($showdescription) $this->showdescription = TRUE;
	}
	
	function __destruct() {
		echo '</ul>';
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
		
		// Only show elements on the first level and only five on the second level, but only if showdescription == FALSE
		if($this->level == 1 || ($this->level == 2 && $this->count[$this->level] <= 5 && $this->showdescription == FALSE))
		{
			$class_names = $value = '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;
			if($this->level == 1 && ($this->count[$this->level] == 5 || $this->count[$this->level] == 9)) $classes[] = 'clear';
			if($this->level == 1) $classes[] = 'span3';

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			if($this->level == 1) 
			{
				$output .= $indent . '<li' . $id . $value . $class_names .'>';
			}
			else
			{
				$output .= '<li>';
			}

			$atts = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
			$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
			
			if($post->post_type != 'imagelink')
			{
				$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
			}

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
				$item_output .= '<a class="subpage-item ext-link" href="'.get_field('protocol', $item->object_id).get_field('link', $item->object_id).'">';
			}
			else
			{
				$item_output .= '<a'. $attributes .'>';
			}

			if($this->level == 1)
			{
				$item_output .= get_the_post_thumbnail($item->object_id, 'page-thumb');
				if($post->post_type == 'imagelink')
				{
					$item_output .= '<div class="ext-icon"></div>';
				}
				$item_output .= $args->link_before.'<h3>'.apply_filters( 'the_title', $item->title, $item->ID ) .'</h3>'. $args->link_after;
			}
			else
			{
				$item_output .= $args->link_before.apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			}

			$item_output .= '</a>';
			$item_output .= $args->after;
			
			if($this->showdescription && $this->level == 1 && get_field('portal_description', $item->object_id))
			{
				$item_output .= '<p>'.get_field('portal_description', $item->object_id).'</p>';
			}
		}

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
	
	function end_el(&$output, $item, $depth=0, $args=array()) {      
		if($this->level == 1 || ($this->level == 2 && $this->count[$this->level] <= 5))
		{
			if($this->level == 1) $output .= "</li>\n";  
			else $output .= "</li>\n";
		}
		elseif($this->level == 2 && $this->count[$this->level] == 6 && $this->showdescription == FALSE)
		{
			$output .= '<li class="more"><a href="'.$this->element->url.'">'. __('Mehr …', 'fau').'</a></li>';
		}
		
	/*	if($this->level == 1)
		{
			if($this->count[$this->level] % 4 == 0) $output .= '</div><div class="row subpages-menu">';
		}
		*/
    }  
    
}


class FAUMenuSubpagesWidget extends WP_Widget
{
	function FAUMenuSubpagesWidget()
	{
		$widget_ops = array('classname' => 'FAUMenuSubpagesWidget', 'description' => __('Bebildertes Menü der Unterseiten', 'fau') );
		$this->WP_Widget('FAUMenuSubpagesWidget', 'Portal-Menü', $widget_ops);
	}

	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'menu-slug' => '', 'title' => '', 'showdescription' => FALSE ) );
		$slug = $instance['menu-slug'];
		$title = $instance['title'];
		$showdescription = $instance['showdescription'];
		
		$menus = get_terms('nav_menu');
		
		echo '<p>';
			echo '<label for="'.$this->get_field_id('title').'">'. __('Titel', 'fau').': </label>';
			echo '<input type="text" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" value="'.$title.'">';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="'.$this->get_field_id('menu-slug').'">' . __('Menü', 'fau') . ': ';
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
		
		echo '<p>';
			echo '<label for="'.$this->get_field_id('showdescription').'">'. __('Beschreibung anzeigen', 'fau') . ': </label>';
			echo '<input type="checkbox" id="'.$this->get_field_id('showdescription').'" name="'.$this->get_field_name('showdescription').'" value="TRUE" ';
				if($showdescription) echo 'checked';
			echo '>';
		echo '</p>';

	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['menu-slug'] = $new_instance['menu-slug'];
		$instance['title'] = $new_instance['title'];
		$instance['showdescription'] = $new_instance['showdescription'];
		$instance['id'] = md5(time());
		return $instance;
	}

	function widget($args, $instance)
	{
		extract($args, EXTR_SKIP);

		echo $before_widget;
		echo '<div class="portal-subpages-item" id="portal-subpages-item-'.$instance['id'].'">';
		if(!empty($instance['title']))	echo '<a href="#portal-subpages-item-'.$instance['id'].'" class="hidden portal-subpages-item-title">'.$instance['title'].'</a>';
		$slug = empty($instance['menu-slug']) ? ' ' : $instance['menu-slug'];
		$showdescription = $instance['showdescription'];

		if (!empty($slug))
		{
			wp_nav_menu( array( 'menu' => $slug, 'container' => false, 'items_wrap' => '%3$s', 'link_before' => '', 'link_after' => '', 'walker' => new Walker_Subpages_Menu($showdescription)));
		}
		echo '</div>';
		echo $after_widget;
	}
}


add_action( 'widgets_init', create_function('', 'return register_widget("FAUMenuSubpagesWidget");') );