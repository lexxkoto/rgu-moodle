<?php

    $manifest = Array(
        'Test Category' => Array (
            'test' => Array(
                'title' => 'Get All Site Users',
                'query' => 'SELECT firstname "First Name", lastname "Last Name", email "Email Address" FROM ___user',
                'capability' => 'moodle/user:viewalldetails'
            ),
        ),
    );

?>
