<?php
defined('MOODLE_INTERNAL') || die();
$messageproviders = [
    // Notify teacher that a student has submitted a quiz attempt
    'activity_created' => [
        'capability' => 'local/custom_notification:emailnotifyactivitycreated',
        'defaults' => [
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
            'airnotifier' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
            'popup' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ],
    ],
];
