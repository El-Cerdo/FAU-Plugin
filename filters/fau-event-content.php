<?php

add_filter('the_content', function($content) {
    if (defined('EVENT_POST_TYPE') && get_post_type() == EVENT_POST_TYPE) {
        global $event_events_helper;
        
        $event = $event_events_helper->get_event(get_the_ID());
        $content = event_get_view($event, $content);
    }
    
    return $content;
    
});

function event_get_view(&$event, &$content) {
    ob_start();

    event_single_view($event);
    
    echo $content;

    event_single_footer_view($event);

    $single_content = ob_get_contents();
    ob_end_clean();

    return $single_content;
}

function event_single_view(&$event) {
    global $event_view_helper,
    $event_calendar_helper,
    $event_settings;

    $subscribe_url = EVENT_EXPORT_URL . "&event_post_ids=$event->post_id";
    $subscribe_url = str_replace('webcal://', 'http://', $subscribe_url);

    $args = array(
        'event' => &$event,
        'recurrence' => $event->recurrence_html,
        'exclude' => $event->exclude_html,
        'categories' => $event->categories_html,
        'tags' => $event->tags_html,
        'location' => nl2br($event->location),
        'contact' => $event->contact_html,
        'subscribe_url' => $subscribe_url
    );
    
    extract( $args );    
    ?>
    <div class="event-detail-item event-category-die-fau">
        <div class="event-date">
            <div class="event-date-month"><?php echo $event->start_month_html ?></div>
            <div class="event-date-day"><?php echo $event->start_day_html ?></div>
        </div>                          
        <div class="event-info event-id-<?php echo $event->post_id ?>">
            <div class="event-time"><?php echo $event->short_timespan_html ?></div>                                
        </div>
        <?php if ($location): ?>
        <div class="event-location"><?php echo $location ?></div>
        <?php endif; ?>
    </div>
    <?php
}
