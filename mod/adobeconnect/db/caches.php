<?php
$definitions = [
        'recordings' => [
                'mode' => cache_store::MODE_APPLICATION,
                'invalidationevents' => ['clearRecCache'],
        ]
];