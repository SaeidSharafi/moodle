<?php

/**
 * Listens for Instant Payment Notification from acecr
 *
 * This script waits for Payment notification from acecr,
 * then double checks that data by sending it back to acecr.
 * If acecr verifies this then it sets up the enrolment for that
 * user.
 *
 * @package    enrol_acecr
 * @copyright  2016 Hossein Harandipour
 * @author     Hossein Harandipour - based on code by others
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\message\message;

require("../../config.php");
require_once("lib.php");
require_once($CFG->libdir . '/deprecatedlib.php');
require_once($CFG->libdir . '/enrollib.php');
require_once($CFG->libdir . '/filelib.php');

set_exception_handler('enrol_acecr_ipn_exception_handler');
$id = required_param('id', PARAM_INT);


if (!$_POST['SaleOrderId']) {
    print_error("تراکنش ناموفق ، لطفا دوباره تلاش نمایید");
    die;
}

error_reporting(E_ALL);

if (empty($_POST)) {
    print_error("دسترسی به این صفحه امکان پذیر نمیباشد");
}

$data = new stdClass();
$data->reservation_number = $_POST['SaleOrderId'];
$data->reference_number = $_POST['SaleReferenceId'];

if (!$transaction = $DB->get_record("enrol_acecr", array("randid" => $data->reservation_number))) {
    message_acecr_error_to_admin("Not a valid reservation_number", $data);
    die;
}
if (!$user = $DB->get_record("user", array("id" => $transaction->userid))) {
    message_acecr_error_to_admin("Not a valid user id", $data);
    die;
}
if (!$course = $DB->get_record("course", array("id" => $transaction->courseid))) {
    message_acecr_error_to_admin("Not a valid course id", $data);
    die;
}
if (!$context = context_course::instance($course->id, IGNORE_MISSING)) {
    message_acecr_error_to_admin("Not a valid context id", $data);
    die;
}
if (!$plugin_instance = $DB->get_record("enrol", array("id" => $transaction->instanceid, "status" => 0))) {
    message_acecr_error_to_admin("Not a valid instance id", $data);
    die;
}

$acecr = new enrol_acecr_plugin();

$results = $acecr->checkPayment($_POST, $transaction);

$data->transaction_state = $results['status'];
$data->transaction_msg = $results['msg'];
$plugin = enrol_get_plugin('acecr');

if ($data->transaction_state == 'success') {

    $transaction->refid = $data->reference_number;
    $transaction->payment_status = $data->transaction_state;
    $transaction->payment_msg = $data->transaction_msg;
    $transaction->timeupdated = time();

    $DB->update_record("enrol_acecr", $transaction);

    if ((float)$plugin_instance->cost <= 0) {
        $cost = (float)$plugin->get_config('cost');
    } else {
        $cost = (float)$plugin_instance->cost;
    }
    $cost = format_float($cost, 0, false);

    if ($plugin_instance->enrolperiod) {
        $timestart = time();
        $timeend = $timestart + $plugin_instance->enrolperiod;
    } else {
        $timestart = 0;
        $timeend = 0;
    }
    $plugin->enrol_user($plugin_instance, $user->id, $plugin_instance->roleid, $timestart, $timeend);

    $coursecontext = context_course::instance($course->id, IGNORE_MISSING);

    if ($users = get_users_by_capability($context, 'moodle/course:update', 'u.*', 'u.id ASC',
        '', '', '', '', false, true)) {
        $users = sort_by_roleassignment_authority($users, $context);
        $teacher = array_shift($users);
    } else {
        $teacher = false;
    }

    $mailstudents = $plugin->get_config('mailstudents');
    $mailteachers = $plugin->get_config('mailteachers');
    $mailadmins = $plugin->get_config('mailadmins');
    $shortname = format_string($course->shortname, true, array('context' => $context));

    if (!empty($mailstudents)) {
        $a = new stdClass();
        $a->coursename = format_string($course->fullname, true, array('context' => $coursecontext));
        $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id";
        $eventdata = new message();
        $eventdata->modulename = 'moodle';
        $eventdata->component = 'enrol_acecr';
        $eventdata->name = 'acecr_enrolment';
        $eventdata->userfrom = empty($teacher) ? core_user::get_support_user() : $teacher;
        $eventdata->userto = $user;
        $eventdata->subject = get_string("enrolmentnew", 'enrol', $shortname);
        $eventdata->fullmessage = get_string('welcometocoursetext', '', $a);
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml = '';
        $eventdata->smallmessage = '';
        message_send($eventdata);

    }

    if (!empty($mailteachers) && !empty($teacher)) {
        $a->course = format_string($course->fullname, true, array('context' => $coursecontext));
        $a->user = fullname($user);

        $eventdata = new message();
        $eventdata->modulename = 'moodle';
        $eventdata->component = 'enrol_acecr';
        $eventdata->name = 'acecr_enrolment';
        $eventdata->userfrom = $user;
        $eventdata->userto = $teacher;
        $eventdata->subject = get_string("enrolmentnew", 'enrol', $shortname);
        $eventdata->fullmessage = get_string('enrolmentnewuser', 'enrol', $a);
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml = '';
        $eventdata->smallmessage = '';
        message_send($eventdata);
    }

    if (!empty($mailadmins)) {
        $a->course = format_string($course->fullname, true, array('context' => $coursecontext));
        $a->user = fullname($user);
        $admins = get_admins();
        foreach ($admins as $admin) {
            $eventdata = new message();
            $eventdata->modulename = 'moodle';
            $eventdata->component = 'enrol_acecr';
            $eventdata->name = 'acecr_enrolment';
            $eventdata->userfrom = $user;
            $eventdata->userto = $admin;
            $eventdata->subject = get_string("enrolmentnew", 'enrol', $shortname);
            $eventdata->fullmessage = get_string('enrolmentnewuser', 'enrol', $a);
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml = '';
            $eventdata->smallmessage = '';
            message_send($eventdata);
        }
    }

    if (!empty($SESSION->wantsurl)) {
        $destination = $SESSION->wantsurl;
        unset($SESSION->wantsurl);
    } else {
        $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
    }

    $fullname = format_string($course->fullname, true, array('context' => $context));
    redirect($destination, get_string('paymentthanks', 'enrol_acecr', $fullname));
} else {
    if (!empty($SESSION->wantsurl)) {
        $destination = $SESSION->wantsurl;
        unset($SESSION->wantsurl);
    } else {
        $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
    }

    $transaction->payment_status = $data->transaction_state;
    $transaction->payment_msg = $data->transaction_msg;
    $transaction->timeupdated = time();

    $DB->update_record("enrol_acecr", $transaction);

    $fullname = format_string($course->fullname, true, array('context' => $context));
    $PAGE->set_url($destination);
    echo $OUTPUT->header();
    $a = new stdClass();
    $a->teacher = get_string('defaultcourseteacher');
    $a->fullname = $fullname;
    notice($data->transaction_msg, $destination);
}

function message_acecr_error_to_admin($subject, $data)
{
    echo $subject;
    $admin = get_admin();
    $site = get_site();

    $message = "$site->fullname:  Transaction failed.\n\n$subject\n\n";

    foreach ($data as $key => $value) {
        $message .= "$key => $value\n";
    }

    $eventdata = new message();
    $eventdata->modulename = 'moodle';
    $eventdata->component = 'enrol_acecr';
    $eventdata->name = 'acecr_enrolment';
    $eventdata->userfrom = $admin;
    $eventdata->userto = $admin;
    $eventdata->subject = "MELLAT ERROR: " . $subject;
    $eventdata->fullmessage = $message;
    $eventdata->fullmessageformat = FORMAT_PLAIN;
    $eventdata->fullmessagehtml = '';
    $eventdata->smallmessage = '';
    message_send($eventdata);
}

/**
 * Silent exception handler.
 *
 * @param Exception $ex
 * @return void - does not return. Terminates execution!
 */
function enrol_acecr_ipn_exception_handler($ex)
{
    $info = get_exception_info($ex);

    $logerrmsg = "enrol_acecr IPN exception handler: " . $info->message;
    if (debugging('', DEBUG_NORMAL)) {
        $logerrmsg .= ' Debug: ' . $info->debuginfo . "\n" . format_backtrace($info->backtrace, true);
    }
    error_log($logerrmsg);

    exit(0);
}
