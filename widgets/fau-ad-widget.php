<?php


class FAUAdWidget extends WP_Widget
{
	function FAUAdWidget()
	{
		$widget_ops = array('classname' => 'FAUAdWidget', 'description' => 'Werbebanner anzeigen' );
		$this->WP_Widget('FAUAdWidget', 'Werbebanner', $widget_ops);
	}

	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'id' => '' ) );
		$id = $instance['id'];
		
		$ads = query_posts('post_type=ad');
		
		echo '<p>';
			echo '<label for="'.$this->get_field_id('id').'">Banner: ';
				echo '<select id="'.$this->get_field_id('id').'" name="'.$this->get_field_name('id').'">';
					foreach($ads as $item)
					{
						echo '<option value="'.$item->ID.'"';
							if($item->ID == attribute_escape($id)) echo ' selected';
						echo '>'.$item->post_title.'</option>';
					}
				echo '</select>';
			echo '</label>';
		echo '</p>';

	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['id'] = $new_instance['id'];
		return $instance;
	}

	function widget($args, $instance)
	{
		extract($args, EXTR_SKIP);

		echo $before_widget;
		echo '<div class="banner-ad">';
		$id = empty($instance['id']) ? ' ' : $instance['id'];

		if (!empty($id))
		{
			$post = get_post($id);
			
			if(get_field('link', $id))	echo '<a href="'.get_field('link', $id).'">';
				echo get_the_post_thumbnail($id, 'full');
			if(get_field('link', $id))	echo '</a>';
		}
		echo '</div>';
		echo $after_widget;
	}
}


add_action( 'widgets_init', create_function('', 'return register_widget("FAUAdWidget");') );