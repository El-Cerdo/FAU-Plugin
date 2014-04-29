<?php
/*
Plugin Name: FAU Plugin
Plugin URI: http://www.fau.de/
Description: Plugin mit Widgets und Post-Types für die FAU-Website
Author: medienreaktor
Version: 1
Author URI: http://www.medienreaktor.de/
*/


require_once('posttypes/fau-posttype-person.php');
require_once('posttypes/fau-posttype-imagelink.php');
require_once('posttypes/fau-posttype-ad.php');
require_once('posttypes/fau-posttype-synonym.php');
require_once('posttypes/fau-posttype-glossary.php');

require_once('widgets/fau-menu-widget.php');
require_once('widgets/fau-person-widget.php');
require_once('widgets/fau-ad-widget.php');
require_once('widgets/fau-logo-widget.php');
require_once('widgets/fau-event-widget.php');
require_once('widgets/fau-tagcloud-widget.php');

require_once('filters/fau-admin.php');
require_once('filters/fau-event-content.php');

require_once('studienangebot-shortcode.php');