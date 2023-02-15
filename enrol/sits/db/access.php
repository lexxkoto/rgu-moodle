<?php

$capabilities = [

    // Enrol anybody.
    'enrol/sits:enrol' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        ],
    ],

    // Manage enrolments of users.
    'enrol/sits:manage' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        ],
    ],

    // Unenrol anybody (including self) - watch out for data loss.
    'enrol/sits:unenrol' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        ],
    ],

    // Unenrol self - watch out for data loss.
    'enrol/sits:unenrolself' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [],
    ],
];

?>
