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

$id         = required_param('id', PARAM_INT);
$groupid    = required_param('groupid', PARAM_INT);
$recscoid   = required_param('recording', PARAM_INT);
$mode   = required_param('mode', PARAM_ALPHA);

global $CFG, $USER, $DB, $OUTPUT;

// Do the usual Moodle setup
if (! $cm = get_coursemodule_from_id('adobeconnect', $id)) {
    print_error('Course Module ID was incorrect');
}
$cond = array('id' => $cm->course);
if (! $course = $DB->get_record('course', $cond)) {
    print_error('Course is misconfigured');
}

$cond = array('id' => $cm->instance);
if (! $adobeconnect = $DB->get_record('adobeconnect', $cond)) {
    print_error('Course module is incorrect');
}

require_login($course, true, $cm);

// ---------- //


// Get HTTPS setting
$https      = false;
$protocol   = 'http://';
if (isset($configs->https) and (!empty($configs->https))) {
    $https      = true;
    $protocol   = 'https://';
}

// Create a Connect Pro login session for this user
$usrobj = new stdClass();
$usrobj = clone($USER);
$login  = $usrobj->username = set_username($usrobj->username, $usrobj->email);

$params = array('instanceid' => $cm->instance, 'groupid' => $groupid);
$sql = "SELECT meetingscoid FROM {adobeconnect_meeting_groups} amg WHERE ".
       "amg.instanceid = :instanceid AND amg.groupid = :groupid";

$meetscoid = $DB->get_record_sql($sql, $params);

// Get the Meeting recording details
$aconnect   = aconnect_login();
$recording  = array();
$fldid      = aconnect_get_folder($aconnect, 'content');
$usrcanjoin = false;
$context = context_module::instance($cm->id);
$data       = aconnect_get_recordings($aconnect, $fldid, $meetscoid->meetingscoid);

/// Set page global
$url = new moodle_url('/mod/adobeconnect/view.php', array('id' => $cm->id));

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(format_string($adobeconnect->name));
$PAGE->set_heading($course->fullname);

if (!empty($data) && array_key_exists($recscoid, $data)) {

    $recording = $data[$recscoid];
} else {

    // If at first you don't succeed ...
    $data2 = aconnect_get_recordings($aconnect, $meetscoid->meetingscoid, $meetscoid->meetingscoid);

    if (!empty($data2) && array_key_exists($recscoid, $data2)) {
        $recording = $data2[$recscoid];
    }
}

aconnect_logout($aconnect);

if (empty($recording) and confirm_sesskey()) {
    notice(get_string('errormeeting', 'adobeconnect'));
   return;
}

// If separate groups is enabled, check if the user is a part of the selected group
if (NOGROUPS != $cm->groupmode) {
    $usrgroups = groups_get_user_groups($cm->course, $USER
    ->id);
    $usrgroups = $usrgroups[0]; // Just want groups and not groupings

    $group_exists = false !== array_search($groupid, $usrgroups);
    $aag          = has_capability('moodle/site:accessallgroups', $context);

    if ($group_exists || $aag) {
        $usrcanjoin = true;
    }
} else {
    $usrcanjoin = true;
}


if (!$usrcanjoin) {
    notice(get_string('usergrouprequired', 'adobeconnect'), $url);
}

// Trigger an event for viewing a recording.
$params = array(
    'relateduserid' => $USER->id,
    'courseid' => $course->id,
    'context' => context_module::instance($id),
);
$event = \mod_adobeconnect\event\adobeconnect_view_recording::create($params);
$event->trigger();

// Include the port number only if it is a port other than 80
$port = '';

if (!empty($configs->port) and (80 != $configs->port)) {
    $port = ':' . $configs->port;
}
$dto = new adobe_connection_dto($configs->host, $configs->port,
    '', '', '', $https, $configs->admin_httpauth);
$aconnect = new connect_class_dom($dto);
$validuser = true;
$aconnect = aconnect_login();
if (!($usrprincipal = aconnect_user_exists($aconnect, $usrobj))) {
    if (!($usrprincipal = aconnect_create_user($aconnect, $usrobj))) {
        // DEBUG
        print_object("error creating user");
        print_object($aconnect->_xmlresponse);
        $validuser = false;
    }
}

$aconnect->request_http_header_login(1, $login);
$adobesession = $aconnect->get_cookie();
if ($mode == 'offline'){
    redirect($protocol . $configs->meethost . $port
        . $recording->url . '?pbMode=offline&page=m&session=' . $aconnect->get_cookie());
}else{
    redirect($protocol . $configs->meethost . $port
        . $recording->url . '?page=m&session=' . $aconnect->get_cookie());
}

