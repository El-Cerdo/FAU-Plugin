<?php


class FAUPersonWidget extends WP_Widget
{
	function FAUPersonWidget()
	{
		$widget_ops = array('classname' => 'FAUPersonWidget', 'description' => 'Personen-Visitenkarte anzeigen' );
		$this->WP_Widget('FAUPersonWidget', 'Personen-Visitenkarte', $widget_ops);
	}

	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'id' => '' ) );
		$id = $instance['id'];
		$title = $instance['title'];
		
		$persons = query_posts('post_type=person');
		
		echo '<p>';
			echo '<label for="'.$this->get_field_id('title').'">Titel: ';
				echo '<input type="text" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" value="'.attribute_escape($title).'" />';
			echo '</label>';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="'.$this->get_field_id('id').'">Person: ';
				echo '<select id="'.$this->get_field_id('id').'" name="'.$this->get_field_name('id').'">';
					foreach($persons as $item)
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
		$instance['title'] = $new_instance['title'];
		return $instance;
	}

	function widget($args, $instance)
	{
		extract($args, EXTR_SKIP);

		echo $before_widget;
		$id = empty($instance['id']) ? ' ' : $instance['id'];
		$title = empty($instance['title']) ? ' ' : $instance['title'];

		if (!empty($id))
		{
			$post = get_post($id);
			
			echo '<div class="person">';
				if(!empty($title)) 					echo '<h2 class="small">'.$title.'</h2>';
				echo get_the_post_thumbnail($id, array(300,150));
				echo '<h3>'.$post->post_title.'</h3>';
				if(get_field('position', $id)) 		echo '<h4>'.get_field('position', $id).'</h4>';
				if(get_field('phone', $id))			echo '<div class="person-info person-info-phone">'.get_field('phone', $id).'</div>';
				if(get_field('fax', $id))			echo '<div class="person-info person-info-fax">'.get_field('fax', $id).'</div>';
				if(get_field('email', $id))			echo '<div class="person-info person-info-email"><a href="mailto:'.get_field('email', $id).'">'.get_field('email', $id).'</a></div>';
			echo '</div>';
		}
		
		echo $after_widget;
	}
}


add_action( 'widgets_init', create_function('', 'return register_widget("FAUPersonWidget");') );