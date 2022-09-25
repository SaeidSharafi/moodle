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

require_once(dirname(__DIR__, 2).'/config.php');
require_once(__DIR__.'/locallib.php');

$id = required_param('id', PARAM_INT); // course_module ID, or
$groupid = required_param('groupid', PARAM_INT);
$sesskey = required_param('sesskey', PARAM_ALPHANUM);

global $CFG, $USER, $DB, $PAGE;
$configs = get_config('mod_adobeconnect');

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

require_login($course, true, $cm);

// Check if the user's email is the Connect Pro user's login
$usrobj = new stdClass();
$usrobj = clone($USER);
$usrobj->username = set_username($usrobj->username, $usrobj->email);

$usrcanjoin = false;

$context = context_module::instance($cm->id);

// If separate groups is enabled, check if the user is a part of the selected group
if (NOGROUPS != $cm->groupmode) {

    $usrgroups = groups_get_user_groups($cm->course, $usrobj->id);
    $usrgroups = $usrgroups[0]; // Just want groups and not groupings

    $group_exists = false !== array_search($groupid, $usrgroups);
    $aag = has_capability('moodle/site:accessallgroups', $context);

    if ($group_exists || $aag) {
        $usrcanjoin = true;
    }
} else {
    $usrcanjoin = true;
}

/// Set page global
$url = new moodle_url('/mod/adobeconnect/view.php', array('id' => $cm->id));

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(format_string($adobeconnect->name));
$PAGE->set_heading($course->fullname);

// user has to be in a group
if ($usrcanjoin and confirm_sesskey($sesskey)) {

    $usrprincipal = 0;
    $validuser = true;

    // Get the meeting sco-id
    $param = array('instanceid' => $cm->instance, 'groupid' => $groupid);
    $meetingscoid = $DB->get_field('adobeconnect_meeting_groups', 'meetingscoid', $param);

    $aconnect = aconnect_login();

    // Check if the meeting still exists in the shared folder of the Adobe server
    $meetfldscoid = aconnect_get_folder($aconnect, 'meetings');
    $filter = array('filter-sco-id' => $meetingscoid);
    $meeting = aconnect_meeting_exists($aconnect, $meetfldscoid, $filter);

    if (!empty($meeting)) {
        $meeting = current($meeting);
    } else {

        /* Check if the module instance has a user associated with it
           if so, then check the user's adobe connect folder for existince of the meeting */
        if (!empty($adobeconnect->userid)) {
            $username = get_connect_username($adobeconnect->userid);
            $meetfldscoid = aconnect_get_user_folder_sco_id($aconnect, $username);
            $meeting = aconnect_meeting_exists($aconnect, $meetfldscoid, $filter);

            if (!empty($meeting)) {
                $meeting = current($meeting);
            }

        }
    }

    // Check the user's capabilities and assign them the Adobe Role
    $validuser = checkUserPemrissions($context, $usrobj, $aconnect, $meetingscoid, $meeting);

    aconnect_logout($aconnect);

    // User is either valid or invalid, if valid redirect user to the meeting url
    if (empty($validuser)) {
        notice(get_string('notparticipant', 'adobeconnect'), $url);
    } else {

        $protocol = 'http://';
        $https = false;
        $login = $usrobj->username;

        if (isset($configs->https) and (!empty($configs->https))) {

            $protocol = 'https://';
            $https = true;
        }

        $dto = new adobe_connection_dto($configs->host, $configs->port,
            '', '', '', $https, $configs->admin_httpauth);

        $aconnect = new connect_class_dom($dto);

        $aconnect->request_http_header_login(1, $login);

        // Include the port number only if it is a port other than 80
        $port = '';

        if (!empty($configs->port) and (80 != $configs->port)) {
            $port = ':'.$configs->port;
        }

        // Trigger an event for joining a meeting.
        $params = array(
            'relateduserid' => $USER->id,
            'courseid'      => $course->id,
            'context'       => context_module::instance($id),
        );
        $event = \mod_adobeconnect\event\adobeconnect_join_meeting::create($params);
        $event->trigger();

        redirect($protocol.$configs->meethost.$port
            .$meeting->url
            .'?session='.$aconnect->get_cookie());
    }
} else {
    notice(get_string('usergrouprequired', 'adobeconnect'), $url);
}

function checkUserPemrissions($context, $usrobj, $aconnect, $meetingscoid, $meeting)
{
    if (!($usrprincipal = aconnect_user_exists($aconnect, $usrobj))) {
        if (!($usrprincipal = aconnect_create_user($aconnect, $usrobj))) {
            // DEBUG
            print_object("error creating user");
            print_object($aconnect->_xmlresponse);
            return false;
        }
    }
    if (empty($meetingscoid) && empty($usrprincipal) && empty($meeting)) {
        notice(get_string('unableretrdetails', 'adobeconnect'), $url);
        return false;
    }

    if (has_capability('mod/adobeconnect:meetinghost', $context, $usrobj->id, false)) {
        if (aconnect_check_user_perm($aconnect, $usrprincipal, $meetingscoid, ADOBE_HOST, true)) {
            //DEBUG
            //                 echo 'host';
            //                 die();
            return true;
        }
        //DEBUG
        print_object('error assign user adobe host role');
        print_object($aconnect->_xmlrequest);
        print_object($aconnect->_xmlresponse);
        return false;

    } elseif (has_capability('mod/adobeconnect:meetingpresenter', $context, $usrobj->id, false)) {
        if (aconnect_check_user_perm($aconnect, $usrprincipal, $meetingscoid, ADOBE_PRESENTER, true)) {
            //DEBUG
            //                 echo 'presenter';
            //                 die();
            return true;
        }
        //DEBUG
        print_object('error assign user adobe presenter role');
        print_object($aconnect->_xmlrequest);
        print_object($aconnect->_xmlresponse);
        return false;
    } elseif (has_capability('mod/adobeconnect:meetingparticipant', $context, $usrobj->id, false)) {
        if (aconnect_check_user_perm($aconnect, $usrprincipal, $meetingscoid, ADOBE_PARTICIPANT, true)) {
            //DEBUG
            //                 echo 'participant';
            //                 die();
            return true;
        }
        //DEBUG
        print_object('error assign user adobe particpant role');
        print_object($aconnect->_xmlrequest);
        print_object($aconnect->_xmlresponse);
        return false;

    } else {
        // Check if meeting is public and allow them to join
        if ($adobeconnect->meetingpublic) {
            // if for a public meeting the user does not not have either of presenter or participant capabilities then give
            // the user the participant role for the meeting
            aconnect_check_user_perm($aconnect, $usrprincipal, $meetingscoid, ADOBE_PARTICIPANT, true);
            return true;
        }
        return false;

    }

}
