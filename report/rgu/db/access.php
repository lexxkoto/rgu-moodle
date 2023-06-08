<?php

$capabilities = [

    // Enrol anybody.
    'report/rgu:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
];

?>
