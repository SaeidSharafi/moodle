<?php
defined('MOODLE_INTERNAL') || die();
$messageproviders = [
    // Notify teacher that a student has submitted a quiz attempt
    'activity_created' => [
        'capability' => 'local/custom_notification:emailnotifyactivitycreated',
        'defaults' => [
            'email' => MESSAGE_PERMITTED,
            'airnotifier' => MESSAGE_PERMITTED,
            'popup' => MESSAGE_PERMITTED,
        ],
    ],
];
