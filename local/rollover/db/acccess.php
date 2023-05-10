<?php

$capabilities = [

    // Minor abuse of Moodle's capability system. Instead of building a UI
    // to let people choose which users are rolled over, we have a new
    // capability. Anyone with this capability will be rolled over.
    // Howard says it's fine.
    'local/rollover:include' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
        ],
    ],
];

?>
