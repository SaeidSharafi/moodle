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
 * @package    mod_adobeconnect
 * @author     Akinsaya Delamarre (adelamarre@remote-learner.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2015 Remote Learner.net Inc http://www.remote-learner.net
 */

use mod_adobeconnect\connect_class_dom;
use mod_adobeconnect\dto\adobe_connection_dto;

require_once(dirname(__FILE__, 3). '/config.php');
require_once(__DIR__. '/lib.php');
require_once(__DIR__. '/locallib.php');


$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$a = optional_param('a', 0, PARAM_INT);  // adobeconnect instance ID
$groupid = optional_param('group', 0, PARAM_INT);

global $CFG, $USER, $DB, $PAGE, $OUTPUT, $SESSION;
//$PAGE->requires->js_call_amd('mod_adobeconnect/ajaxcall', 'init');

$configs = get_config('mod_adobeconnect');

if ($id) {
    if (!$cm = get_coursemodule_from_id('adobeconnect', $id)) {
        print_error('Course Module ID was incorrect');
    }

    $cond = array('id' => $cm->course);
    if (!$course = $DB->get_record('course', $cond)) {
        print_error('Course is misconfigured');
    }

    $cond = array('id' => $cm->instance);
    if (!$adobeconnect = $DB->get_record('adobeconnect', $cond)) {
        print_error('Course module is incorrect');
    }

} else if ($a) {

    $cond = array('id' => $a);
    if (!$adobeconnect = $DB->get_record('adobeconnect', $cond)) {
        print_error('Course module is incorrect');
    }

    $cond = array('id' => $adobeconnect->course);
    if (!$course = $DB->get_record('course', $cond)) {
        print_error('Course is misconfigured');
    }
    if (!$cm = get_coursemodule_from_instance('adobeconnect', $adobeconnect->id, $course->id)) {
        print_error('Course Module ID was incorrect');
    }

} else {
    print_error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

// Check for submitted data
if (($formdata = data_submitted($CFG->wwwroot . '/mod/adobeconnect/view.php')) && confirm_sesskey()) {

    // Edit participants
    if (isset($formdata->participants)) {

        $cond = array('shortname' => 'adobeconnectpresenter');
        $roleid = $DB->get_field('role', 'id', $cond);

        if (!empty($roleid)) {
            redirect("participants.php?id=$id&contextid={$context->id}&roleid=$roleid&groupid={$formdata->group}", '', 0);
        } else {
            $message = get_string('nopresenterrole', 'adobeconnect');
            $OUTPUT->notification($message);
        }
    }
}

// Check if the user's email is the Connect Pro user's login
$usrobj = new stdClass();
$usrobj = clone($USER);

$usrobj->username = set_username($usrobj->username, $usrobj->email);

/// Print the page header
$url = new moodle_url('/mod/adobeconnect/view.php', array('id' => $cm->id));

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(format_string($adobeconnect->name));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

$stradobeconnects = get_string('modulenameplural', 'adobeconnect');
$stradobeconnect = get_string('modulename', 'adobeconnect');

$params = array('instanceid' => $cm->instance);
$sql = "SELECT meetingscoid " .
        "FROM {adobeconnect_meeting_groups} amg " .
        "WHERE amg.instanceid = :instanceid ";

$meetscoids = $DB->get_records_sql($sql, $params);

// Check if the user exists and if not create the new user

$usrprincipal = checkUser($usrobj);


// Log in the current user
$login = $usrobj->username;
$password = $usrobj->username;

$dto = new adobe_connection_dto($configs->host,
    $configs->port,
    $configs->admin_login,
    $configs->admin_password,
    '',
    isset($configs->https) && !empty($configs->https),
    $configs->admin_httpauth);

$aconnect = new connect_class_dom($dto);

$aconnect->request_http_header_login(1, $login);
$adobesession = $aconnect->get_cookie();

// The batch of code below handles the display of Moodle groups
if ($cm->groupmode) {

    $querystring = array('id' => $cm->id);
    $url = new moodle_url('/mod/adobeconnect/view.php', $querystring);

    // Retrieve a list of groups that the current user can see/manage
    $user_groups = groups_get_activity_allowed_groups($cm, $USER->id);

    if ($user_groups) {

        // Print groups selector drop down
        groups_print_activity_menu($cm, $url, false, true);

        // Retrieve the currently active group for the user's session
        $groupid = groups_get_activity_group($cm);

        /* Depending on the series of events groups_get_activity_group will
         * return a groupid value of  0 even if the user belongs to a group.
         * If the groupid is set to 0 then use the first group that the user
         * belongs to.
         */
        $aag = has_capability('moodle/site:accessallgroups', $context);

        if (0 == $groupid) {
            $groups = groups_get_user_groups($cm->course, $USER->id);
            $groups = current($groups);

            if (!empty($groups)) {

                $groupid = key($SESSION->activegroup[$cm->course]);
            } else if ($aag) {
                /* If the user does not explicitely belong to any group
                 * check their capabilities to see if they have access
                 * to manage all groups; and if so display the first course
                 * group by default
                 */
                $groupid = key($user_groups);
            }
        }
    }
}

$aconnect = aconnect_login();

// Get the Meeting details
$cond = array('instanceid' => $adobeconnect->id, 'groupid' => $groupid);
$scoid = $DB->get_field('adobeconnect_meeting_groups', 'meetingscoid', $cond);

$meetfldscoid = aconnect_get_folder($aconnect, 'meetings');

$filter = array('filter-sco-id' => $scoid);

if (($meeting = aconnect_meeting_exists($aconnect, $meetfldscoid, $filter))) {
    $meeting = current($meeting);
} else {

    /* First check if the module instance has a user associated with it
       if so, then check the user's adobe connect folder for existince of the meeting */
    if (!empty($adobeconnect->userid)) {
        $username = get_connect_username($adobeconnect->userid);
        $meetfldscoid = aconnect_get_user_folder_sco_id($aconnect, $username);
        $meeting = aconnect_meeting_exists($aconnect, $meetfldscoid, $filter);

        if (!empty($meeting)) {
            $meeting = current($meeting);
        }
    }

    // If meeting does not exist then display an error message
    if (empty($meeting)) {

        $message = get_string('nomeeting', 'adobeconnect');
        echo $OUTPUT->notification($message);
        aconnect_logout($aconnect);
        die();
    }
}

aconnect_logout($aconnect);

$sesskey = !empty($usrobj->sesskey) ? $usrobj->sesskey : '';

$renderer = $PAGE->get_renderer('mod_adobeconnect');

$meetingdetail = new stdClass();
$meetingdetail->name = html_entity_decode($meeting->name);

// Determine if the Meeting URL is to appear
if (has_capability('mod/adobeconnect:meetingpresenter', $context) or
        has_capability('mod/adobeconnect:meetinghost', $context)) {

    // Include the port number only if it is a port other than 80
    $port = '';

    if (!empty($configs->port) and (80 != $configs->port)) {
        $port = ':' . $configs->port;
    }

    $protocol = 'http://';

    if ($configs->https) {
        $protocol = 'https://';
    }

    $url = $protocol . $configs->meethost . $port
            . $meeting->url;

    $meetingdetail->url = $url;

    $url = $protocol . $configs->meethost . $port . '/admin/meeting/sco/info?principal-id=' .
            $usrprincipal . '&amp;sco-id=' . $scoid . '&amp;session=' . $adobesession;

    // Get the server meeting details link
    $meetingdetail->servermeetinginfo = $url;

} else {
    $meetingdetail->url = '';
    $meetingdetail->servermeetinginfo = '';
}

// Determine if the user has the permissions to assign perticipants
$meetingdetail->participants = false;

if (has_capability('mod/adobeconnect:meetingpresenter', $context, $usrobj->id) ||
        has_capability('mod/adobeconnect:meetinghost', $context, $usrobj->id)) {

    $meetingdetail->participants = true;
}

//  CONTRIB-2929 - remove date format and let Moodle decide the format
// Get the meeting start time
$time = userdate($adobeconnect->starttime);
$meetingdetail->starttime = $time;

// Get the meeting end time
$time = userdate($adobeconnect->endtime);
$meetingdetail->endtime = $time;

// Get the meeting intro text
$meetingdetail->intro = $adobeconnect->intro;
$meetingdetail->introformat = $adobeconnect->introformat;

echo $OUTPUT->box_start('generalbox', 'meetingsummary');

// If groups mode is enabled for the activity and the user belongs to a group
if (NOGROUPS != $cm->groupmode && 0 != $groupid) {

    echo $renderer->display_meeting_detail($meetingdetail, $id, $groupid);
} else if (NOGROUPS == $cm->groupmode) {

    // If groups mode is disabled
    echo $renderer->display_meeting_detail($meetingdetail, $id, $groupid);
} else {

    // If groups mode is enabled but the user is not in a group
    echo $renderer->display_no_groups_message();
}

echo $OUTPUT->box_end();

echo '<br />';

// Check if meeting is private, if so check the user's capability.  If public show recorded meetings
if (!$adobeconnect->meetingpublic) {

    // Check capabilities
    if (has_capability('mod/adobeconnect:meetingpresenter', $context, $usrobj->id) or
            has_capability('mod/adobeconnect:meetingparticipant', $context, $usrobj->id)) {
        $can_view_recordings = true;
    }
} else {

    // Check group mode and group membership
    $can_view_recordings = true;
}

// Lastly check group mode and group membership
if (NOGROUPS != $cm->groupmode && 0 != $groupid) {
    $can_view_recordings = $can_view_recordings && true;
} else if (NOGROUPS == $cm->groupmode) {
    $showrecording = $can_view_recordings && true;
} else {
    $showrecording = $can_view_recordings && false;
}

//check permissions for recording & attendances
$can_view_recordings = false;
$can_mange_recordings = false;
$can_view_attendees = false;
if (has_capability('mod/adobeconnect:viewattendees', $context, $usrobj->id) ||
        has_capability('moodle/site:config', $context, $usrobj->id)) {
    $can_view_attendees = true;
}
if (has_capability('mod/adobeconnect:managerecordings', $context, $usrobj->id) ||
        has_capability('moodle/site:config', $context, $usrobj->id)) {
    $can_mange_recordings = true;
} else {
    $can_mange_recordings = false;
}
if (has_capability('mod/adobeconnect:viewrecordings', $context, $usrobj->id) ||
        has_capability('moodle/site:config', $context, $usrobj->id)) {
    $can_view_recordings = true;
} else {
    $can_view_recordings = false;
}


$showoffline = 0;
echo $OUTPUT->box_start('details', 'details-adobe');
echo "<div class='loader-full'><span class='spinner-border'></span> </div>";
echo $renderer->display_controllers($adobeconnect,$cm->id, $scoid, $cm->groupmode, $usrprincipal, $can_view_attendees, $can_mange_recordings);
if ($can_view_recordings) {
    $recordings = getRecordings($context, $cm->id, $usrobj);

    if (!empty($recordings)) {
        if ($configs->showoffline) {
            $showoffline = $adobeconnect->showoffline;

            if (is_null($showoffline)) {
                $showoffline = 1;
            }
        } else {
            $showoffline = 0;
        }
        if (has_capability('moodle/site:config', $context, $usrobj->id)) {
            $showoffline = 1;
        }
        echo $renderer->display_meeting_recording($recordings, $cm->id, $groupid, $scoid, $showoffline);
    } else {
        echo $OUTPUT->box_start('recordings', 'records');
        echo $OUTPUT->box_end();
    }

    //
}
if ($can_view_attendees || $configs->view_own_attendance) {
    $attendees = getAttendances($context, $cm->id);

    if (!empty($attendees)) {
        // Echo the rendered HTML to the page
        echo $renderer->display_meeting_attendees($attendees, $cm->id, $groupid, $scoid);
        //echo $renderer->display_meeting_attendees_csv($attendees, $cm->id, $groupid, $scoid);
    } else {
        echo $OUTPUT->box_start('recordings', 'attendances');
        echo $OUTPUT->box_end();
    }

    //
}
echo $OUTPUT->box_end();
$params = [
        'locale' => current_language(),
        'contextid' => (string) $cm->id,
        'scoid' => serialize($meetscoids),
        'groupmode' => (int) $groupid,
        'usrprincipal' => (int) $usrprincipal,
        'showoffline' => $showoffline,
        'sync' => true,
];

$PAGE->requires->js_call_amd('mod_adobeconnect/sync', 'init', [$params]);

// Trigger an event for joining a meeting.
$params = array(
        'relateduserid' => $USER->id,
        'courseid' => $course->id,
        'context' => context_module::instance($cm->id)
);

$event = \mod_adobeconnect\event\adobeconnect_view::create($params);
$event->trigger();

/// Finish the page
echo $OUTPUT->footer();


function checkUser($usrobj) {
    $aconnect = aconnect_login();
    if (!($usrprincipal = aconnect_user_exists($aconnect, $usrobj))) {
        if (!($usrprincipal = aconnect_create_user($aconnect, $usrobj))) {
            // DEBUG

            debugging(print_r($usrprincipal,true), DEBUG_DEVELOPER);
            debugging("error creating user", DEBUG_DEVELOPER);

            //            print_object("error creating user");
            //            print_object($aconnect->_xmlresponse);
            $validuser = false;
        }
    }
    return $usrprincipal;
}
