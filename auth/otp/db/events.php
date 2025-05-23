<?php

$observers = array(
    array(
        'eventname'   => '\core\event\user_loggedin',
        'callback'    => '\auth_otp\observer::user_loggedin',
    ),
);
