<?php


class FAUPersonWidget extends WP_Widget
{
	function FAUPersonWidget()
	{
		$widget_ops = array('classname' => 'FAUPersonWidget', 'description' => __('Personen-Visitenkarte anzeigen', 'fau') );
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
			echo '<label for="'.$this->get_field_id('title').'">'. __('Titel', 'fau'). ': ';
				echo '<input type="text" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" value="'.attribute_escape($title).'" />';
			echo '</label>';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="'.$this->get_field_id('id').'">' . __('Person', 'fau'). ': ';
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
			
			
			$content = '<div class="person">';
				if(!empty($title)) 					$content .= '<h2 class="small">'.$title.'</h2>';
				
				$content .= '<div class="row">';
				
					if(has_post_thumbnail($id))
					{
						$content .= '<div class="span1">';
							$content .= get_the_post_thumbnail($id, 'person-thumb');
						$content .= '</div>';
					}
					
					$content .= '<div class="span3">';
						$content .= '<h3>';
							if(get_field('title', $id)) 	$content .= get_field('title', $id).' ';
							if(get_field('firstname', $id)) 	$content .= get_field('firstname', $id).' ';
							if(get_field('lastname', $id)) 		$content .= get_field('lastname', $id);
							if(get_field('title_suffix', $id)) 	$content .= ' '.get_field('title_suffix', $id);
						$content .= '</h3>';
						$content .= '<ul class="person-info">';
							if(get_field('position', $id)) 		$content .= '<li class="person-info person-info-position"><strong>'.get_field('position', $id).'</strong></li>';
							if(get_field('institution', $id))	$content .= '<li class="person-info person-info-institution">'.get_field('institution', $id).'</li>';
							if(get_field('phone', $id))			$content .= '<li class="person-info person-info-phone">'.get_field('phone', $id).'</li>';
							if(get_field('fax', $id))			$content .= '<li class="person-info person-info-fax">'.get_field('fax', $id).'</li>';
							if(get_field('email', $id))			$content .= '<li class="person-info person-info-email"><a href="mailto:'.get_field('email', $id).'">'.get_field('email', $id).'</a></li>';
							if(get_field('webseite', $id))		$content .= '<li class="person-info person-info-www"><a href="http://'.get_field('webseite', $id).'">'.get_field('webseite', $id).'</a></li>';
							if(get_field('adresse', $id))		$content .= '<li class="person-info person-info-address">'.get_field('adresse', $id).'</li>';
							if(get_field('raum', $id))			$content .= '<li class="person-info person-info-room">Raum '.get_field('raum', $id).'</li>';
							//	if(get_field('freitext', $id))		$content .= '<div class="person-info person-info-description">'.get_field('freitext', $id).'</div>';
						$content .= '</ul>';
					$content .= '</div>';
				$content .= '</div>';
			
			$content .= '</div>';
		}
		
		echo $content;
		
		echo $after_widget;
	}
}


add_action( 'widgets_init', create_function('', 'return register_widget("FAUPersonWidget");') );