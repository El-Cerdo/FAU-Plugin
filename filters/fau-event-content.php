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
    <table class="full-event event-meta single-event event-id-<?php echo $event->post_id ?> <?php if( $event->multiday ) echo 'multiday' ?> <?php if( $event->allday ) echo 'allday' ?>">
        <tbody>
            <tr>
                <th scope="row"><?php _e( 'Wann:' ) ?></th>
                <td colspan="2">
                    <?php echo $event->timespan_html ?>
                </td>
            </tr>
            <?php if( $location ): ?>
            <tr>
                <th scope="row" class="event-location"><?php _e( 'Wo:' ) ?></th>
                <td colspan="2"><?php echo $location ?></td>
            </tr>
            <?php endif; ?>
            <?php if( $categories ): ?>
            <tr>
                <th scope="row" class="event-categories"><?php _e( 'Kategorien:' ) ?></th>
                <td class="event-categories"><?php echo $categories ?></td>
            </tr>
            <?php endif ?>
            <?php if( $tags ): ?>
            <tr>
                <th scope="row" class="event-tags"><?php _e( 'Tags:' ) ?></th>
                <td class="event-tags"><?php echo $tags ?></td>
            </tr>
            <?php endif ?>

        </tbody>
    </table>
<?php /*    <p>
        <a href="<?php echo esc_attr( $subscribe_url ); ?>" title="<?php _e( 'Abonniere diesen Kalender in Ihrem bevorzugtem Programm (iCal, Outlook, etc.)' ) ?>">
            <?php _e( 'Abonniere' ) ?>
        </a>
    </p>    */ ?>
    <?php
}

function event_single_footer_view(&$event) {    
  /*  if( $event->ical_feed_url ): ?>
    <p>
        <cite>
            <?php echo sprintf( __( 'Dieser Beitrag wurde aus einer externe <a href="%s" title="ICS-Kalender-Quelle">Kalender-Quelle</a> importiert.' ), esc_attr( str_replace( 'http://', 'webcal://', $event->ical_feed_url ) ) ) ?>
        </cite>
        <cite>
        <?php if( $event->ical_source_url ): ?>
            <a href="<?php echo esc_attr( $event->ical_source_url ) ?>" target="_blank"><?php _e( 'Originalartikel' ) ?></a>
        <?php endif ?>
        </cite>
    </p>
    <?php endif; */
}
