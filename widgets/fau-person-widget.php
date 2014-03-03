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
		
		//$persons = query_posts('post_type=person');
		$persons = get_posts(array('post_type' => 'person', 'posts_per_page' => 9999));
		
		if($item->post_title)
		{
			$name = $item->post_title;
		}
		else
		{
			$name = $this->get_field_id('firstname').' '.$this->get_field_id('lastname');
		}
		
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
				
				echo '<div class="row">';
				
					if(has_post_thumbnail($id))
					{
						echo '<div class="span1">';
							echo get_the_post_thumbnail($id, 'person-thumb');
						echo '</div>';
					}
					
					echo '<div class="span3">';
						echo '<h3>';
							if(get_field('title', $id)) 	echo get_field('title', $id).' ';
							if(get_field('firstname', $id)) 	echo get_field('firstname', $id).' ';
							if(get_field('lastname', $id)) 		echo get_field('lastname', $id);
						echo '</h3>';
						if(get_field('position', $id)) 		echo '<h4>'.get_field('position', $id).'</h4>';
						if(get_field('institution', $id))			echo '<div class="person-info person-info-institution">'.get_field('institution', $id).'</div>';
						if(get_field('phone', $id))			echo '<div class="person-info person-info-phone">'.get_field('phone', $id).'</div>';
						if(get_field('fax', $id))			echo '<div class="person-info person-info-fax">'.get_field('fax', $id).'</div>';
						if(get_field('email', $id))			echo '<div class="person-info person-info-email"><a href="mailto:'.get_field('email', $id).'">'.get_field('email', $id).'</a></div>';
						if(get_field('webseite', $id))		echo '<div class="person-info person-info-www"><a href="http://'.get_field('webseite', $id).'">'.get_field('webseite', $id).'</a></div>';
						if(get_field('adresse', $id))		echo '<div class="person-info person-info-address">'.get_field('adresse', $id).'</div>';
						if(get_field('raum', $id))			echo '<div class="person-info person-info-room">Raum '.get_field('raum', $id).'</div>';
						if(get_field('freitext', $id))		echo '<div class="person-info person-info-description">'.get_field('freitext', $id).'</div>';
						
					echo '</div>';
				echo '</div>';
			
			echo '</div>';
		}
		
		echo $after_widget;
	}
}


add_action( 'widgets_init', create_function('', 'return register_widget("FAUPersonWidget");') );