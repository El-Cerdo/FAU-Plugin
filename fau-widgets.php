<?php
/*
Plugin Name: FAU Plugin
Plugin URI: http://www.fau.de/
Description: Widgets für die FAU-Website
Author: medienreaktor
Version: 1
Author URI: http://www.medienreaktor.de/
*/

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
			wp_nav_menu( array( 'menu' => $slug, 'container' => false, 'items_wrap' => '<ul class="%2$s">%3$s</ul>' ));
		}
		
		echo $after_widget;
	}
}


add_action( 'widgets_init', create_function('', 'return register_widget("FAUMenuSubpagesWidget");') );?>