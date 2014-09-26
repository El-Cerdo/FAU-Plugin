<?php
add_shortcode('termine', 'events_shortcode');

function events_shortcode($atts, $content = "") {
    global $event_events_helper, $event_calendar_helper;

    if (!defined('EVENT_POST_TYPE') || empty($event_events_helper) || empty($event_calendar_helper)) {
        return sprintf('<p>%s</p>', __('Das Termin-Plugin ist nicht vorhanden.'));
    }

    $atts = shortcode_atts(
        array(
            'kategorien' => '',     // Trenne Kategorien-Slugs durch Kommas
            'schlagworte' => '',    // Trenne Schlagworte-Slugs durch Kommas
            'anzahl' => 10,         // Anzahl der Termine
            'page_link' => 0,       // Permalink zu einer Seite die bspw. eine Liste der Termine erzeugt
            'abonnement_link' => 0  // Abonnement-Link anzeigen
        ), $atts
    );

    $anzahl = intval($atts['anzahl']);
    if ($anzahl < 1) {
        $anzahl = 1;
    }

    $terms = explode(',', $atts['kategorien']);
    $terms = array_map('trim', $terms);
    
    $kategorien = array();
    foreach($terms as $value) {
        $term = get_term_by('slug', $value, 'event_category');
        if(empty($term)) {
            continue;
        }        
        $kategorien[] = $term->term_id;
    }
    
    $terms = explode(',', $atts['schlagworte']);
    $terms = array_map('trim', $terms);
    
    $schlagworte = array();
    foreach($terms as $value) {
        $term = get_term_by('slug', $value, 'event_tag');
        if(empty($term)) {
            continue;
        }
        $schlagworte[] = $term->term_id;
    }
    
    $page_link = !empty($atts['page_link']) ? (int)$atts['page_link'] : false;
    
    if($page_link > 0) {
        $page_link = get_permalink($page_link);
    }
        
    $abonnement_link = !empty($atts['abonnement_link']) ? true : false;

    if($abonnement_link) {
        $subscribe_filter = '';
        $subscribe_filter .= $kategorien ? '&event_cat_ids=' . implode(',', $kategorien) : '';
        $subscribe_filter .= $schlagworte ? '&event_tag_ids=' . implode(',', $schlagworte) : '';
        
        $subscribe_url = EVENT_EXPORT_URL . $subscribe_filter;
    }
    
    $timestamp = $event_events_helper->gmt_to_local(time());

    $limit = array(
        'cat_ids' => $kategorien,
        'tag_ids' => $schlagworte
    );

    if((!empty($atts['kategorien']) && empty($kategorien)) || (!empty($atts['schlagworte']) && empty($schlagworte))) {
        $anzahl = 0;
    }    
    $event_results = $event_calendar_helper->get_events_relative_to($timestamp, $anzahl, 0, $limit);
    $dates = $event_calendar_helper->get_agenda_date_array($event_results['events']);
    
    ob_start();
    ?>
    <div class="events-list">
        <?php if (!$dates): ?>
            <p><?php _e('Keine bevorstehenden Termine'); ?></p>
        <?php else: ?>
            <ul>
                <?php foreach ($dates as $timestamp => $date_info): ?>
                    <?php foreach ($date_info['events'] as $category): ?>
                        <?php foreach ($category as $event): ?>
                            <?php $cat = get_the_terms($event->post_id, 'event_category'); ?>
                            <li class="<?php foreach ($cat as $c) : echo ' event-category-' . $c->slug; endforeach; ?><?php if (isset($date_info['today']) && $date_info['today']) echo ' event-today'; ?>">
                                <div class="event-date"><div class="event-date-month"><?php echo date_i18n('M', $timestamp, TRUE); ?></div><div class="event-date-day"><?php echo date_i18n('d', $timestamp, TRUE); ?></div></div>                          
                                <div class="event-info event-id-<?php echo $event->post_id; ?><?php if ($event->allday) : echo 'event-allday'; endif; ?>"><?php if (!$event->allday): ?><div class="event-time"><?php echo esc_html(sprintf(__('%s Uhr bis %s Uhr'), $event->start_time, $event->end_time)) ?></div><?php endif; ?>
                                <div class="event-title"><a href="<?php echo esc_attr(get_permalink($event->post_id)); ?>"><?php echo esc_html(apply_filters('the_title', $event->post->post_title)); ?></a></div>
                                <div class="event-location"><?php if (!empty($event->venue)): ?><?php echo sprintf(__('%s'), $event->venue); ?><?php endif; ?></div></div>
                            </li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <?php if($page_link): ?>
                <li>
                    <div class="events-more-links"><a class="events-more" href="<?php echo $page_link; ?>"><?php _e('Mehr Veranstaltungen'); ?></a></div>
                </li>
                <?php endif; ?>
                <?php if($abonnement_link): ?>
                <li>
                    <div class="events-more-links"><a class="events-more" href="<?php echo $subscribe_url; ?>"><?php _e('Abonnement'); ?></a></div>
                </li>
                <?php endif; ?>                
            </ul>
    <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
