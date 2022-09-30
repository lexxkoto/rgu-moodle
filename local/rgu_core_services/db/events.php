<?php
    
    defined('MOODLE_INTERNAL') || die();
    
    $observers = array(
        array(
            'eventname' => '\core\event\user_created',
            'callback'  => 'local_rgu_core_services_observer::user_created',
        ),
        array(
            'eventname' => '\core\event\user_updated',
            'callback'  => 'local_rgu_core_services_observer::user_updated',
        ),
        array(
            'eventname' => '\core\event\user_loggedin',
            'callback' => 'local_rgu_core_services_observer::user_loggedin',
        ),
        array(
            'eventname' => '\core\event\user_loggedinas',
            'callback' => 'local_rgu_core_services_observer::user_loggedinas',
        ),
        
    );