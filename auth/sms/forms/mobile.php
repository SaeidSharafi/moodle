<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * user signup page.
 *
 * @package    auth_sms
 * @subpackage auth
 * @copyright  2022 Morteza Ahmadi <m.ahmadi.ma@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/auth/sms/locallib.php');
require_once($CFG->dirroot . '/auth/sms/panel/sms.ir/SmsIR_VerificationCode.php');
require_once($CFG->dirroot . '/auth/sms/panel/sms.ir/new_verification_code.php');
require_once($CFG->dirroot . '/auth/sms/panel/magfa/magfa.php');
require_once($CFG->dirroot . '/login/lib.php');

if (!$authplugin = signup_is_enabled()) {
    print_error('notlocalisederrormessage', 'error', '', 'Sorry, you may not use this page.');
}

$PAGE->set_url('/auth/sms/forms/mobile.php');
$PAGE->set_context(context_system::instance());

// If wantsurl is empty or /login/signup.php, override wanted URL.
// We do not want to end up here again if user clicks "Login".
if (empty($SESSION->wantsurl)) {
    $SESSION->wantsurl = $CFG->wwwroot . '/';
} else {
    $wantsurl = new moodle_url($SESSION->wantsurl);
    if ($PAGE->url->compare($wantsurl, URL_MATCH_BASE)) {
        $SESSION->wantsurl = $CFG->wwwroot . '/';
    }
}

$mform_signup = $authplugin->signup_form();

if ($mform_signup->is_cancelled()) {
    auth_sms_clear_session();
    redirect(get_login_url());
} else if ($data = $mform_signup->get_data()) {

    if($data->type == 'phone2') {
        //print_error('notlocalisederrormessage', 'error', '', 'Sorry, you may not use this page.');
        redirect($CFG->wwwroot . '/login/signup.php');
        exit;
    } elseif($data->type == 'forgotten_password'){
        if(isset($authplugin->config->smsforgottenpassword) && $authplugin->config->smsforgottenpassword) {
            $phone2 = substr($SESSION->phone2, -10, 10); //09123456789 and +989123456789 is the same;
            $phone_place = auth_sms_get_phone_place();
            if($phone_place == 'phone2') {
                $params = array(
                    'phone2' => '%' . $phone2, //09123456789 and +989123456789 is the same
                );
                $user = $DB->get_record_sql("SELECT * FROM {user} WHERE phone2 LIKE :phone2 AND deleted <> 1", $params);
            } else {
                $params = array(
                    'phone2' => '%' . $phone2, //09123456789 and +989123456789 is the same
                    'fieldid' => $phone_place,
                );
                $user = $DB->get_record_sql("SELECT u.* FROM {user} u RIGHT JOIN {user_info_data} uid ON u.id = uid.userid WHERE uid.fieldid = :fieldid AND data LIKE :phone2 AND u.deleted <> 1", $params);
            }
            if($user) {
                $hashedpassword = hash_internal_user_password($data->password);
                $DB->set_field('user', 'password', $hashedpassword, array('id' => $user->id));
                auth_sms_clear_session();
                notice(get_string('change_password_success', 'auth_sms'), "$CFG->wwwroot/login/index.php");
            } else {
                print_error('not_exist_error', 'auth_sms');
            }
        } else {
            print_error('error', 'core');
        }
    } elseif($data->type == 'signup') {
        print_error('notlocalisederrormessage', 'error', '', 'Sorry, you may not use this page.');
        $phone_place = auth_sms_get_phone_place();
        if($phone_place == 'phone2') {
            $data->phone2 = $SESSION->phone2;
        } else {
            //profile field
            $field = $DB->get_record('user_info_field', ['id' => $phone_place]);
            $data->{'profile_field_' . $field->shortname} = $SESSION->phone2;
        }
        //Session should be cleared before signup_setup_new_user and user_signup, because after those,
        //script is finished.
        auth_sms_clear_session();
        $user = signup_setup_new_user($data);
        $authplugin->user_signup($user, true);
    } else {
        print_error('error', 'core');
    }
}

//for errors
$newaccount = get_string('smsforgottenpassword','auth_sms');
$login      = get_string('login');

$PAGE->navbar->add($login);
$PAGE->navbar->add($newaccount);

$PAGE->set_pagelayout('login');
$PAGE->set_title($newaccount);
$PAGE->set_heading($SITE->fullname);

echo $OUTPUT->header();

//copy from login\index.php
if(true) {
    if (isloggedin() and !isguestuser()) {
        // prevent logging when already logged in, we do not want them to relogin by accident because sesskey would be changed
        echo $OUTPUT->box_start();
        $logout = new single_button(new moodle_url('/login/logout.php', array('sesskey' => sesskey(),'loginpage'=>1)), get_string('logout'), 'post');
        $continue = new single_button(new moodle_url('/'), get_string('cancel'), 'get');
        echo $OUTPUT->confirm(get_string('alreadyloggedin', 'error', fullname($USER)), $logout, $continue);
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        exit;
    }
}

try {
    $renderer = $PAGE->get_renderer('auth_' . $authplugin->authtype);
} catch (coding_exception $ce) {
    // Fall back on the general renderer.
    $renderer = $OUTPUT;
}
echo $renderer->render($mform_signup);
echo $OUTPUT->footer();
