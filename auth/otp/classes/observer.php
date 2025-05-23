<?php

namespace auth_otp;

defined('MOODLE_INTERNAL') || die();

class observer {
    public static function user_loggedin(\core\event\user_loggedin $event) {
        global $DB;

        $userid = $event->userid;
        $user = $DB->get_record('user', array('id' => $userid));

        if ($user->auth === 'otp') {
            $DB->execute('UPDATE {user} set auth = "manual" where id = :id', ['id' => $userid]);
        }
    }
}
