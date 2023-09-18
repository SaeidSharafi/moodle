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
global $CFG;

use mod_adobeconnect\connect_class_dom;
use mod_adobeconnect\dto\adobe_connection_dto;

require_once($CFG->dirroot . '/user/profile/lib.php');

define('ADOBE_VIEW_ROLE', 'view');
define('ADOBE_HOST_ROLE', 'host');
define('ADOBE_MINIADMIN_ROLE', 'mini-host');
define('ADOBE_REMOVE_ROLE', 'remove');

define('ADOBE_PARTICIPANT', 1);
define('ADOBE_PRESENTER', 2);
define('ADOBE_REMOVE', 3);
define('ADOBE_HOST', 4);

define('ADOBE_TEMPLATE_POSTFIX', '- Template');
define('ADOBE_MEETING_POSTFIX', '- Meeting');

define('ADOBE_MEETPERM_PUBLIC',
        0); //means the Acrobat Connect meeting is public, and anyone who has the URL for the meeting can enter the room.
define('ADOBE_MEETPERM_PROTECTED',
        1); //means the meeting is protected, and only registered users and accepted guests can enter the room.
define('ADOBE_MEETPERM_PRIVATE', 2); // means the meeting is private, and only registered users and participants can enter the room

define('ADOBE_TMZ_LENGTH', 6);
$configs = get_config('mod_adobeconnect');


function adobe_connection_test($emaillogin,$host = '', $port = 80, $username = '',
        $password = '', $httpheader = '',
         $https = false) {
    $configs = get_config('mod_adobeconnect');
    if (empty($host) or
            empty($port) or (0 == $port) or
            empty($username) or
            empty($password) or
            empty($httpheader)) {

        echo "</p>One of the required parameters is blank or incorrect: <br />" .
                "Host: $host<br /> Port: $port<br /> Username: $username<br /> Password: $password" .
                "<br /> HTTP Header: $httpheader</p>";

        die();
    }

    $messages = array();
    $dto = new adobe_connection_dto($host,
            $port,
            $username,
            $password,
            '',
            $https, $configs->admin_httpauth);
    $aconnectDOM = new connect_class_dom($dto);

    $params = array(
            'action' => 'common-info'
    );

    // Send common-info call to obtain the session key
    echo '<p>Sending common-info call:</p>';
    $aconnectDOM->create_request($params);

    if (!empty($aconnectDOM->_xmlresponse)) {

        // Get the session key from the XML response
        $aconnectDOM->read_cookie_xml($aconnectDOM->_xmlresponse);

        $cookie = $aconnectDOM->get_cookie();
        if (empty($cookie)) {

            echo '<p>unable to obtain session key from common-info call</p>';
            echo '<p>xmlrequest:</p>';
            $doc = new DOMDocument();

            if ($doc->loadXML($aconnectDOM->_xmlrequest)) {
                echo '<p>' . htmlspecialchars($doc->saveXML()) . '</p>';
            } else {
                echo '<p>unable to display the XML request</p>';
            }

            echo '<p>xmlresponse:</p>';
            $doc = new DOMDocument();

            if ($doc->loadXML($aconnectDOM->_xmlresponse)) {
                echo '<p>' . htmlspecialchars($doc->saveHTML()) . '</p>';
            } else {
                echo '<p>unable to display the XML response</p>';
            }

        } else {

            // print success
            echo '<p style="color:#006633">successfully obtained the session key: ' . $aconnectDOM->get_cookie() . '</p>';

            // test logging in as the administrator
            $params = array(
                    'action' => 'login',
                    'login' => $aconnectDOM->get_username(),
                    'password' => $aconnectDOM->get_password(),
            );

            $aconnectDOM->create_request($params);

            if ($aconnectDOM->call_success('adobe_connection_test')) {
                echo '<p style="color:#006633">successfully logged in as admin user</p>';
                //$username

                //Test retrevial of folders
                echo '<p>Testing retrevial of shared content, recording and meeting folders:</p>';
                $folderscoid = aconnect_get_folder($aconnectDOM, 'content');

                if ($folderscoid) {
                    echo '<p style="color:#006633">successfully obtained shared content folder scoid: ' . $folderscoid . '</p>';
                } else {

                    echo '<p>error obtaining shared content folder</p>';
                    echo '<p style="color:#680000">XML request:<br />' . htmlspecialchars($aconnectDOM->_xmlrequest) . '</p>';
                    echo '<p style="color:#680000">XML response:<br />' . htmlspecialchars($aconnectDOM->_xmlresponse) . '</p>';

                }

                $folderscoid = aconnect_get_folder($aconnectDOM, 'forced-archives');

                if ($folderscoid) {
                    echo '<p style="color:#006633">successfully obtained forced-archives (meeting recordings) folder scoid: ' .
                            $folderscoid . '</p>';
                } else {

                    echo '<p>error obtaining forced-archives (meeting recordings) folder</p>';
                    echo '<p style="color:#680000">XML request:<br />' . htmlspecialchars($aconnectDOM->_xmlrequest) . '</p>';
                    echo '<p style="color:#680000">XML response:<br />' . htmlspecialchars($aconnectDOM->_xmlresponse) . '</p>';

                }

                $folderscoid = aconnect_get_folder($aconnectDOM, 'meetings');

                if ($folderscoid) {
                    echo '<p style="color:#006633">successfully obtained meetings folder scoid: ' . $folderscoid . '</p>';
                } else {

                    echo '<p>error obtaining meetings folder</p>';
                    echo '<p style="color:#680000">XML request:<br />' . htmlspecialchars($aconnectDOM->_xmlrequest) . '</p>';
                    echo '<p style="color:#680000">XML response:<br />' . htmlspecialchars($aconnectDOM->_xmlresponse) . '</p>';

                }

                //Test creating a meeting
                $folderscoid = aconnect_get_folder($aconnectDOM, 'meetings');

                $meeting = new stdClass();
                $meeting->name = 'testmeetingtest';
                $time = time();
                $meeting->starttime = $time;
                $time = $time + (60 * 60);
                $meeting->endtime = $time;
                // $attendace = aconnect_report_meeting_attaendace($aconnectDOM, $meeting, $folderscoid);

                if (($meetingscoid = aconnect_create_meeting($aconnectDOM, $meeting, $folderscoid))) {
                    echo '<p style="color:#006633">successfully created meeting <b>testmeetingtest</b> scoid: ' . $meetingscoid .
                            '</p>';
                } else {

                    echo '<p>error creating meeting <b>testmeetingtest</b> folder</p>';
                    echo '<p style="color:#680000">XML request:<br />' . htmlspecialchars($aconnectDOM->_xmlrequest) . '</p>';
                    echo '<p style="color:#680000">XML response:<br />' . htmlspecialchars($aconnectDOM->_xmlresponse) . '</p>';
                }

                //Test creating a user
                $user = new stdClass();
                $user->username = 'testusertest';
                $user->firstname = 'testusertest';
                $user->lastname = 'testusertest';
                $user->email = 'testusertest@test.com';

                if (!empty($emaillogin)) {
                    $user->username = $user->email;
                }

                $skipdeletetest = false;

                if (!($usrprincipal = aconnect_user_exists($aconnectDOM, $user))) {
                    $usrprincipal = aconnect_create_user($aconnectDOM, $user);
                    if ($usrprincipal) {
                        echo '<p style="color:#006633">successfully created user <b>testusertest</b> principal-id: ' .
                                $usrprincipal . '</p>';
                    } else {
                        echo '<p>error creating user  <b>testusertest</b></p>';
                        echo '<p style="color:#680000">XML request:<br />' . htmlspecialchars($aconnectDOM->_xmlrequest) . '</p>';
                        echo '<p style="color:#680000">XML response:<br />' . htmlspecialchars($aconnectDOM->_xmlresponse) . '</p>';

                        aconnect_logout($aconnectDOM);
                        die();
                    }
                } else {

                    echo '<p>user <b>testusertest</b> already exists skipping delete user test</p>';
                    $skipdeletetest = true;
                }

                //Test assigning a user a role to the meeting
                if (aconnect_check_user_perm($aconnectDOM, $usrprincipal, $meetingscoid, ADOBE_PRESENTER, true)) {
                    echo '<p style="color:#006633">successfully assigned user <b>testusertest</b>' .
                            ' presenter role in meeting <b>testmeetingtest</b>: ' . $usrprincipal . '</p>';
                } else {
                    echo '<p>error assigning user <b>testusertest</b> presenter role in meeting <b>testmeetingtest</b></p>';
                    echo '<p style="color:#680000">XML request:<br />' . htmlspecialchars($aconnectDOM->_xmlrequest) . '</p>';
                    echo '<p style="color:#680000">XML response:<br />' . htmlspecialchars($aconnectDOM->_xmlresponse) . '</p>';
                }

                //Test removing role from meeting
                if (aconnect_check_user_perm($aconnectDOM, $usrprincipal, $meetingscoid, ADOBE_REMOVE_ROLE, true)) {
                    echo '<p style="color:#006633">successfully removed presenter role for user <b>testusertest</b>' .
                            ' in meeting <b>testmeetingtest</b>: ' . $usrprincipal . '</p>';
                } else {
                    echo '<p>error remove presenter role for user <b>testusertest</b> in meeting <b>testmeetingtest</b></p>';
                    echo '<p style="color:#680000">XML request:<br />' . htmlspecialchars($aconnectDOM->_xmlrequest) . '</p>';
                    echo '<p style="color:#680000">XML response:<br />' . htmlspecialchars($aconnectDOM->_xmlresponse) . '</p>';
                }

                //Test removing user from server
                if (!$skipdeletetest) {
                    if (aconnect_delete_user($aconnectDOM, $usrprincipal)) {
                        echo '<p style="color:#006633">successfully removed user <b>testusertest</b> principal-id: ' .
                                $usrprincipal . '</p>';
                    } else {
                        echo '<p>error removing user <b>testusertest</b></p>';
                        echo '<p style="color:#680000">XML request:<br />' . htmlspecialchars($aconnectDOM->_xmlrequest) . '</p>';
                        echo '<p style="color:#680000">XML response:<br />' . htmlspecialchars($aconnectDOM->_xmlresponse) . '</p>';
                    }
                }

                //Test removing meeting from server
                if ($meetingscoid) {
                    if (aconnect_remove_meeting($aconnectDOM, $meetingscoid)) {
                        echo '<p style="color:#006633">successfully removed meeting <b>testmeetingtest</b> scoid: ' .
                                $meetingscoid . '</p>';
                    } else {
                        echo '<p>error removing meeting <b>testmeetingtest</b> folder</p>';
                        echo '<p style="color:#680000">XML request:<br />' . htmlspecialchars($aconnectDOM->_xmlrequest) . '</p>';
                        echo '<p style="color:#680000">XML response:<br />' . htmlspecialchars($aconnectDOM->_xmlresponse) . '</p>';
                    }
                }

            } else {
                echo '<p style="color:#680000">logging in as ' . $username .
                        ' was not successful, check to see if the username and password are correct </p>';
            }

        }

    } else {
        echo '<p style="color:#680000">common-info API call returned an empty document.  Please check your settings and try again </p>';
    }

    aconnect_logout($aconnectDOM);

}

/**
 * Returns the folder sco-id
 *
 * @param object an adobe connection_class object
 * @param string $folder name of the folder to get
 * (ex. forced-archives = recording folder | meetings = meetings folder
 * | content = shared content folder)
 * @return mixed adobe connect folder sco-id || false if there was an error
 *
 */
function aconnect_get_folder($aconnect, $folder = '') {
    $folderscoid = false;
    $params = array('action' => 'sco-shortcuts');

    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_get_folder')) {
        $folderscoid = aconnect_get_folder_sco_id($aconnect->_xmlresponse, $folder);
        //        $params = array('action' => 'sco-contents', 'sco-id' => $folderscoid);
    }

    return $folderscoid;
}

/**
 * TODO: comment function and return something meaningful
 */
function aconnect_get_folder_sco_id($xml, $folder) {
    $scoid = false;

    $dom = new DomDocument();
    $dom->loadXML($xml);

    $domnodelist = $dom->getElementsByTagName('sco');

    if (!empty($domnodelist->length)) {

        for ($i = 0; $i < $domnodelist->length; $i++) {

            $domnode = $domnodelist->item($i)->attributes->getNamedItem('type');

            if (!is_null($domnode)) {

                if (0 == strcmp($folder, $domnode->nodeValue)) {
                    $domnode = $domnodelist->item($i)->attributes->getNamedItem('sco-id');

                    if (!is_null($domnode)) {
                        $scoid = (int) $domnode->nodeValue;

                    }
                }
            }
        }
    }

    return $scoid;

}

/**
 * Log in as the admin user.  This should only be used to conduct API calls.
 */
function aconnect_login() {
    global $CFG, $USER, $COURSE;
    $configs = get_config('mod_adobeconnect');
    if (!isset($configs->host) or
            !isset($configs->admin_login) or
            !isset($configs->admin_password)) {
        if (is_siteadmin($USER->id)) {
            notice(get_string('adminnotsetupproperty', 'adobeconnect'),
                    $CFG->wwwroot . '/admin/settings.php?section=modsettingadobeconnect');
        } else {
            notice(get_string('notsetupproperty', 'adobeconnect'),
                    '', $COURSE);
        }
    }

    $dto = new adobe_connection_dto($configs->host,
        $configs->port,
        $configs->admin_login,
        $configs->admin_password,
        '',
        isset($configs->https) && !empty($configs->https),
        $configs->admin_httpauth);

    $aconnect = new connect_class_dom($dto);

    $params = array(
            'action' => 'common-info'
    );

    $aconnect->create_request($params);

    $aconnect->read_cookie_xml($aconnect->_xmlresponse);

    $params = array(
            'action' => 'login',
            'login' => $aconnect->get_username(),
            'password' => $aconnect->get_password(),
    );

    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_login')) {
        $aconnect->set_connection(1);
    } else {
        $aconnect->set_connection(0);
    }

    return $aconnect;
}

/**
 * Logout
 *
 * @param object $aconnect - connection object
 * @return true on success else false
 */
function aconnect_logout(&$aconnect) {
    if (!$aconnect->get_connection()) {
        return true;
    }

    $params = array('action' => 'logout');
    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_logout')) {
        $aconnect->set_connection(0);
        return true;
    } else {
        $aconnect->set_connection(1);
        return false;
    }
}

/**
 * Calls all operations needed to retrieve and return all
 * templates defined in the shared templates folder and meetings
 *
 * @param object $aconnect connection object
 * @return array $templates an array of templates
 */
function aconnect_get_templates_meetings($aconnect) {
    $templates = array();
    $meetings = array();
    $meetfldscoid = false;
    $tempfldscoid = false;

    $params = array(
            'action' => 'sco-shortcuts',
    );

    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_get_templates_meetings')) {
        // Get shared templates folder sco-id
        $tempfldscoid = aconnect_get_shared_templates($aconnect->_xmlresponse);
    }

    if (false !== $tempfldscoid) {
        $params = array(
                'action' => 'sco-expanded-contents',
                'sco-id' => $tempfldscoid,
        );

        $aconnect->create_request($params);

        if ($aconnect->call_success('aconnect_get_templates_meetings #2')) {
            $templates = aconnect_return_all_templates($aconnect->_xmlresponse);
        }
    }

    //    if (false !== $meetfldscoid) {
    //        $params = array(
    //            'action' => 'sco-expanded-contents',
    //            'sco-id' => $meetfldscoid,
    //            'filter-type' => 'meeting',
    //        );
    //
    //        $aconnect->create_request($params);
    //
    //        if ($aconnect->call_success()) {
    //            $meetings = aconnect_return_all_meetings($aconnect->_xmlresponse);
    //        }
    //
    //    }

    return $templates + $meetings;
}

/**
 * Parse XML looking for shared-meeting-templates attribute
 * and returning the sco-id of the folder
 *
 * @param string $xml returned XML from a sco-shortcuts call
 * @return mixed sco-id if found or false if not found or error
 */
function aconnect_get_shared_templates($xml) {
    $scoid = false;

    $dom = new DomDocument();
    $dom->loadXML($xml);

    $domnodelist = $dom->getElementsByTagName('shortcuts');

    if (!empty($domnodelist->length)) {

        //        for ($i = 0; $i < $domnodelist->length; $i++) {

        $innerlist = $domnodelist->item(0)->getElementsByTagName('sco');

        if (!empty($innerlist->length)) {

            for ($x = 0; $x < $innerlist->length; $x++) {

                if ($innerlist->item($x)->hasAttributes()) {

                    $domnode = $innerlist->item($x)->attributes->getNamedItem('type');

                    if (!is_null($domnode)) {

                        if (0 == strcmp('shared-meeting-templates', $domnode->nodeValue)) {
                            $domnode = $innerlist->item($x)->attributes->getNamedItem('sco-id');

                            if (!is_null($domnode)) {
                                $scoid = (int) $domnode->nodeValue;
                            }
                        }
                    }
                }
            }
        }
        //        }

    }

    return $scoid;
}

function aconnect_return_all_meetings($xml) {
    $meetings = array();
    $xml = new SimpleXMLElement($xml);

    if (empty($xml)) {
        return $meetings;
    }

    foreach ($xml->{'expanded-scos'}[0]->sco as $key => $sco) {
        if (0 == strcmp('meeting', $sco['type'])) {
            $mkey = (int) $sco['sco-id'];
            $meetings[$mkey] = (string) current($sco->name) . ' ' . ADOBE_MEETING_POSTFIX;
        }
    }

    return $meetings;
}

/**
 * Parses XML for meeting templates and returns an array
 * with sco-id as the key and template name as the value
 *
 * @param strimg $xml XML returned from a sco-expanded-contents call
 * @return array of templates sco-id -> key, name -> value
 */
function aconnect_return_all_templates($xml) {
    $templates = array();

    $dom = new DomDocument();
    $dom->loadXML($xml);

    $domnodelist = $dom->getElementsByTagName('expanded-scos');

    if (!empty($domnodelist->length)) {

        $innerlist = $domnodelist->item(0)->getElementsByTagName('sco');

        if (!empty($innerlist->length)) {

            for ($i = 0; $i < $innerlist->length; $i++) {

                if ($innerlist->item($i)->hasAttributes()) {
                    $domnode = $innerlist->item($i)->attributes->getNamedItem('type');

                    if (!is_null($domnode) and 0 == strcmp('meeting', $domnode->nodeValue)) {
                        $domnode = $innerlist->item($i)->attributes->getNamedItem('sco-id');

                        if (!is_null($domnode)) {
                            $tkey = (int) $domnode->nodeValue;
                            $namelistnode = $innerlist->item($i)->getElementsByTagName('name');

                            if (!is_null($namelistnode)) {
                                $name = $namelistnode->item(0)->nodeValue;
                                $templates[$tkey] = (string) $name . ' ' . ADOBE_TEMPLATE_POSTFIX;
                            }
                        }
                    }
                }
            }
        }
    }

    return $templates;
}

/**
 * Delete selected recording
 *
 * @param object $aconnect a connect_class object
 * @param int $folderscoid the recordings folder sco-id
 * @param int $sourcescoid the meeting sco-id
 * @param string $url reference url to redirect
 *
 */
function aconnect_delete_recordings($aconnect, $sourcescoid) {


    $params = array('action' => 'sco-delete',
            'sco-id' => $sourcescoid,

    );
    $aconnect->create_request($params);
    $xmlObject = new SimpleXMLElement($aconnect->_xmlresponse);
    $status = $xmlObject->status['code'];
    if ($status == "no-data") {
        return array(
                'status' => 0,
                'is_notification' => 0,
                'msg' => 'recording not found',
                'data' => ''
        );
        //redirect($url, "recordings not found", 3, \core\output\notification::NOTIFY_WARNING);
    } else if ($status == "ok") {
        return array(
                'status' => 1,
                'is_notification' => 0,
                'msg' => 'recording has been removed',
                'data' => ''
        );
        //redirect($url, "recording has been removed", 3, \core\output\notification::NOTIFY_SUCCESS);
    }

}

/**
 * Returns information about all recordings that belong to a specific
 * meeting sco-id
 *
 * @param object $aconnect a connect_class object
 * @param int $folderscoid the recordings folder sco-id
 * @param int $sourcescoid the meeting sco-id
 *
 * @return mixed array an array of object with the recording sco-id
 * as the key and the recording properties as properties
 */
function aconnect_get_recordings($aconnect, $folderscoid, $sourcescoid) {
    $params = array('action' => 'sco-contents',
            'sco-id' => $folderscoid,
            'sort-date-created' => 'desc',
    );

    // Check if meeting scoid and folder scoid are the same
    // If hey are the same then that means that forced recordings is not
    // enabled filter-source-sco-id should not be included.  If the
    // meeting scoid and folder scoid are not equal then forced recordings
    // are enabled and we can use filter by filter-source-sco-id
    // Thanks to A. gtdino
    if ($sourcescoid != $folderscoid) {
        $params['filter-source-sco-id'] = $sourcescoid;
    }

    $aconnect->create_request($params);
    $recordings = array();

    if ($aconnect->call_success('aconnect_get_recordings')) {

        $dom = new DomDocument();
        $dom->loadXML($aconnect->_xmlresponse);
        //        $xmlObject = new SimpleXMLElement($aconnect->_xmlresponse);
        //        var_dump($xmlObject);
        //        var_dump($aconnect->_xmlresponse);
        $domnodelist = $dom->getElementsByTagName('scos');

        if (!empty($domnodelist->length)) {

            //            for ($i = 0; $i < $domnodelist->length; $i++) {

            $innernodelist = $domnodelist->item(0)->getElementsByTagName('sco');

            if (!empty($innernodelist->length)) {

                for ($x = 0; $x < $innernodelist->length; $x++) {

                    if ($innernodelist->item($x)->hasAttributes()) {

                        $domnode = $innernodelist->item($x)->attributes->getNamedItem('sco-id');

                        if (!is_null($domnode)) {
                            $meetingdetail = $innernodelist->item($x);
                            // Check if the SCO item is a recording or uploaded document.  We only want to display recordings
                            //In AC9, the recording length info is stored as an attributed of 'sco'
                            $recordingvac9 = $meetingdetail->attributes->getNamedItem('duration');
                            //In AC-8 and before, the recording length info is stored as its own element
                            $recordingvac8 = $meetingdetail->getElementsByTagName('duration')->item(0);
                            //In AC9, many objects have a 'recording' attribute defined, but only recordings have a non-empty value.
                            // So check the attribute has a value (in minutes, can be rounded to 0 if short, so can't use !empty())
                            // Check if the SCO item is a recording or uploaded document.  We only want to display recordings
                            if ((!is_null($recordingvac9) && $recordingvac9->nodeValue !== '') || !is_null($recordingvac8)) {

                                $j = (int) $domnode->nodeValue;

                                $recordings[$j] = new stdClass();
                                $value = (!is_null($meetingdetail->getElementsByTagName('name'))) ?
                                        $meetingdetail->getElementsByTagName('name')->item(0)->nodeValue : '';

                                $recordings[$j]->name = (string) $value;

                                $value = (!is_null($meetingdetail->getElementsByTagName('url-path'))) ?
                                        $meetingdetail->getElementsByTagName('url-path')->item(0)->nodeValue : '';

                                $recordings[$j]->url = (string) $value;

                                $value = (!is_null($meetingdetail->getElementsByTagName('date-begin'))) ?
                                        $meetingdetail->getElementsByTagName('date-begin')->item(0)->nodeValue : '';

                                $recordings[$j]->startdate = (string) $value;

                                $value = (!is_null($meetingdetail->getElementsByTagName('date-end'))) ?
                                        $meetingdetail->getElementsByTagName('date-end')->item(0)->nodeValue : '';

                                $recordings[$j]->enddate = (string) $value;

                                $value = (!is_null($meetingdetail->getElementsByTagName('date-created'))) ?
                                        $meetingdetail->getElementsByTagName('date-created')->item(0)->nodeValue : '';

                                $recordings[$j]->createdate = (string) $value;

                                $value = (!is_null($meetingdetail->getElementsByTagName('date-modified'))) ?
                                        $meetingdetail->getElementsByTagName('date-modified')->item(0)->nodeValue : '';

                                $recordings[$j]->modified = (string) $value;

                                $value = (!is_null($meetingdetail->attributes->getNamedItem('duration'))) ?
                                        $meetingdetail->attributes->getNamedItem('duration')->nodeValue : '';

                                $recordings[$j]->duration = (string) $value;

                                $recordings[$j]->sourcesco = (int) $sourcescoid;
                            }

                        }
                    }
                }
            }
            //            }

            return $recordings;
        } else {
            return false;
        }
    } else {
        return false;
    }
    //

}

function aconnect_hide_recordings($instanceid, $sourcescoid) {
    global $DB, $CFG;
    $sql = "SELECT id" .
            "FROM {adobeconnect_recordings} ac " .
            "WHERE instanceid = {$instanceid} AND recordingscoid = {$sourcescoid}";

}

/**
 * Returns information about all recordings that belong to a specific
 * meeting sco-id
 *
 * @param obj $aconnect a connect_class object
 * @param int $folderscoid the recordings folder sco-id
 * @param int $sourcescoid the meeting sco-id
 *
 * @return mixed array an array of object with the attendance sco-id
 * as the key and the attendance properties as properties
 */
function aconnect_get_attendance($aconnect, $folderscoid, $sourcescoid) {
    $params = array('action' => 'report-meeting-attendance',
            'sco-id' => $folderscoid,
            'sort-participant-name' => 'asc',
            'sort-date-created' => 'desc',
        //'filter-source-sco-id' => $sourcescoid,
    );

    // Check if meeting scoid and folder scoid are the same
    // If hey are the same then that means that forced recordings is not
    // enabled filter-source-sco-id should not be included.  If the
    // meeting scoid and folder scoid are not equal then forced recordings
    // are enabled and we can use filter by filter-source-sco-id
    // Thanks to A. gtdino
    if ($sourcescoid != $folderscoid) {
        $params['filter-source-sco-id'] = $sourcescoid;
    }

    $aconnect->create_request($params);
    $attendances = array();

    if ($aconnect->call_success('aconnect_get_attendance')) {

        $dom = new DomDocument();

        $dom->loadXML($aconnect->_xmlresponse);

        $domnodelist = $dom->getElementsByTagName('report-meeting-attendance');

        if (!empty($domnodelist->length)) {

            //            for ($i = 0; $i < $domnodelist->length; $i++) {

            $innernodelist = $domnodelist->item(0)->getElementsByTagName('row');

            if (!empty($innernodelist->length)) {

                for ($x = 0; $x < $innernodelist->length; $x++) {

                    if ($innernodelist->item($x)->hasAttributes()) {

                        $domnode = $innernodelist->item($x)->attributes->getNamedItem('principal-id');

                        if (!is_null($domnode)) {
                            $meetingdetail = $innernodelist->item($x);

                            // Check if the SCO item is a recording or uploaded document.  We only want to display recordings

                            $j = (int) $domnode->nodeValue;
                            if (!array_key_exists($j, $attendances)) {
                                $attendances[$j] = new stdClass();
                            }

                            $value = (!is_null($meetingdetail->getElementsByTagName('login'))) ?
                                    $meetingdetail->getElementsByTagName('login')->item(0)->nodeValue : '';

                            $attendances[$j]->name = (string) $value;

                            $value = (!is_null($meetingdetail->getElementsByTagName('session-name'))) ?
                                    $meetingdetail->getElementsByTagName('session-name')->item(0)->nodeValue : '';

                            $attendances[$j]->session_name = (string) $value;

                            $value = (!is_null($meetingdetail->getElementsByTagName('sco-name'))) ?
                                    $meetingdetail->getElementsByTagName('sco-name')->item(0)->nodeValue : '';

                            $attendances[$j]->sco_name = (string) $value;

                            $end_date = (!is_null($meetingdetail->getElementsByTagName('date-end'))) ?
                                    $meetingdetail->getElementsByTagName('date-end')->item(0)->nodeValue : '';
                            $start_date = (!is_null($meetingdetail->getElementsByTagName('date-created'))) ?
                                    $meetingdetail->getElementsByTagName('date-created')->item(0)->nodeValue : '';

                            $dates = ["start_date" => (string) $start_date, "end_date" => (string) $end_date];

                            $attendances[$j]->dates[] = $dates;

                            $value = (!is_null($meetingdetail->getElementsByTagName('participant-name'))) ?
                                    $meetingdetail->getElementsByTagName('participant-name')->item(0)->nodeValue : '';

                            $attendances[$j]->participant_name = (string) $value;

                            //                                $value = (!is_null($meetingdetail->attributes->getNamedItem('duration'))) ?
                            //                                    $meetingdetail->attributes->getNamedItem('duration')->nodeValue : '';
                            //
                            //
                            //                                $attendances[$j]->duration = (string)$value;

                            $attendances[$j]->sourcesco = (int) $sourcescoid;

                        }
                    }
                }
            }
            //            }
            return $attendances;
        } else {
            return false;
        }
    } else {
        return false;
    }

}

/**
 * Parses XML and returns the meeting sco-id
 *
 * @param string XML obtained from a sco-update call
 */
function aconnect_get_meeting_scoid($xml) {
    $scoid = false;

    $dom = new DomDocument();
    $dom->loadXML($xml);

    $domnodelist = $dom->getElementsByTagName('sco');

    if (!empty($domnodelist->length)) {
        if ($domnodelist->item(0)->hasAttributes()) {
            $domnode = $domnodelist->item(0)->attributes->getNamedItem('sco-id');

            if (!is_null($domnode)) {
                $scoid = (int) $domnode->nodeValue;
            }
        }
    }

    return $scoid;
}

/**
 * Update meeting
 *
 * @param obj $aconnect connect_class object
 * @param obj $meetingobj an adobeconnect module object
 * @param int $meetingfdl adobe connect meeting folder sco-id
 * @return bool true if call was successful else false
 */
function aconnect_update_meeting($aconnect, $meetingobj, $meetingfdl) {
    $params = array('action' => 'sco-update',
            'sco-id' => $meetingobj->scoid,
            'name' => htmlentities($meetingobj->name, ENT_COMPAT, 'UTF-8'),
            'folder-id' => $meetingfdl,
        // updating meeting URL using the API corrupts the meeting for some reason
        //                    'url-path' => '/'.$meetingobj->meeturl,
            'date-begin' => $meetingobj->starttime,
            'date-end' => $meetingobj->endtime,
    );

    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_update_meeting')) {
        return true;
    } else {
        return false;
    }

}

/**
 * Update a meeting's access permissions
 *
 * @param obj $aconnect connect_class object
 * @param int $meetingscoid meeting sco-id
 * @param int $perm meeting permission id
 * @return bool true if call was successful else false
 */
function aconnect_update_meeting_perm($aconnect, $meetingscoid, $perm) {
    $params = array('action' => 'permissions-update',
            'acl-id' => $meetingscoid,
            'principal-id' => 'public-access',
    );

    switch ($perm) {
        case ADOBE_MEETPERM_PUBLIC:
            $params['permission-id'] = 'view-hidden';
            break;
        case ADOBE_MEETPERM_PROTECTED:
            $params['permission-id'] = 'remove';
            break;
        case ADOBE_MEETPERM_PRIVATE:
        default:
            $params['permission-id'] = 'denied';
            break;
    }

    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_update_meeting_perm')) {
        return true;
    } else {
        return false;
    }

}

/** CONTRIB-1976, CONTRIB-1992
 * This function adds a fraction of a second to the ISO 8601 date
 *
 * @param int $time unix timestamp
 * @return mixed a string (ISO 8601) containing the decimal fraction of a second
 * or false if it was not able to determine where to put it
 */
function aconnect_format_date_seconds($time) {

    $newdate = false;
    $date = date("c", $time);

    $pos = strrpos($date, '-');
    $length = strlen($date);

    $diff = $length - $pos;

    if ((0 < $diff) and (ADOBE_TMZ_LENGTH == $diff)) {
        $firstpart = substr($date, 0, $pos);
        $lastpart = substr($date, $pos);
        $newdate = $firstpart . '.000' . $lastpart;

        return $newdate;
    }

    $pos = strrpos($date, '+');
    $length = strlen($date);

    $diff = $length - $pos;

    if ((0 < $diff) and (ADOBE_TMZ_LENGTH == $diff)) {
        $firstpart = substr($date, 0, $pos);
        $lastpart = substr($date, $pos);
        $newdate = $firstpart . '.000' . $lastpart;

        return $newdate;

    }

    return false;
}

/**
 * Creates a meeting
 *
 * @param obj $aconnect connect_class object
 * @param obj $meetingobj an adobeconnect module object
 * @param int $meetingfdl adobe connect meeting folder sco-id
 * @return mixed meeting sco-id on success || false on error
 */
function aconnect_create_meeting($aconnect, $meetingobj, $meetingfdl) {
    //date("Y-m-d\TH:i
    //    $params['url-path'] = uniqidReal(12);
    //    var_dump($params['url-path']);
    //    die();
    $starttime = aconnect_format_date_seconds($meetingobj->starttime);
    $endtime = aconnect_format_date_seconds($meetingobj->endtime);

    if (empty($starttime) or empty($endtime)) {
        $message = 'Failure (aconnect_find_timezone) in finding the +/- sign in the date timezone' .
                "\n" . date("c", $meetingobj->starttime) . "\n" . date("c", $meetingobj->endtime);
        debugging($message, DEBUG_DEVELOPER);
        return false;
    }

    $params = array('action' => 'sco-update',
            'type' => 'meeting',
            'name' => htmlentities($meetingobj->name, ENT_COMPAT, 'UTF-8'),
            'folder-id' => $meetingfdl,
            'date-begin' => $starttime,
            'date-end' => $endtime,
    );
    // report-meeting-attendance
    if (!empty($meetingobj->meeturl)) {
        $params['url-path'] = $meetingobj->meeturl;
    }

    if (!empty($meetingobj->templatescoid)) {
        $params['source-sco-id'] = $meetingobj->templatescoid;
    }

    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_create_meeting')) {
        return aconnect_get_meeting_scoid($aconnect->_xmlresponse);
    } else {
        return false;
    }
}

/**
 * Creates a meeting
 *
 * @param obj $aconnect connect_class object
 * @param obj $meetingobj an adobeconnect module object
 * @param int $meetingfdl adobe connect meeting folder sco-id
 * @return mixed meeting sco-id on success || false on error
 */
function aconnect_report_meeting_attaendace($aconnect, $meetingobj, $meetingfdl) {
    //date("Y-m-d\TH:i

    $starttime = aconnect_format_date_seconds($meetingobj->starttime);
    $endtime = aconnect_format_date_seconds($meetingobj->endtime);

    if (empty($starttime) or empty($endtime)) {
        $message = 'Failure (aconnect_find_timezone) in finding the +/- sign in the date timezone' .
                "\n" . date("c", $meetingobj->starttime) . "\n" . date("c", $meetingobj->endtime);
        debugging($message, DEBUG_DEVELOPER);
        return false;
    }

    $params = array('action' => 'report-meeting-attendance',
            'sco-id' => '121351',
            'sort-participant-name' => 'asc',
    );
    // report-meeting-attendance
    //    if (!empty($meetingobj->meeturl)) {
    //        $params['url-path'] = $meetingobj->meeturl;
    //    }
    //
    //    if (!empty($meetingobj->templatescoid)) {
    //        $params['source-sco-id'] = $meetingobj->templatescoid;
    //    }

    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_report_meeting_attaendace')) {
        //var_dump($aconnect->_xmlresponse);
        return aconnect_get_meeting_scoid($aconnect->_xmlresponse);
    } else {
        return false;
    }
}

/**
 * Finds a matching meeting sco-id
 *
 * @param object $aconnect a connect_class object
 * @param int $meetfldscoid Meeting folder sco-id
 * @param array $filter array key is the filter and array value is the value
 * (ex. array('filter-name' => 'meeting101'))
 * @return mixed array of objects with sco-id as key and meeting name and url as object
 * properties as value || false if not found or error occured
 */
function aconnect_meeting_exists($aconnect, $meetfldscoid, $filter = array()) {
    $matches = array();

    $params = array(
            'action' => 'sco-contents',
            'sco-id' => $meetfldscoid,
            'filter-type' => 'meeting',
    );

    if (empty($filter)) {
        return false;
    }

    $params = array_merge($params, $filter);
    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_meeting_exists')) {
        $dom = new DomDocument();
        $dom->loadXML($aconnect->_xmlresponse);

        $domnodelist = $dom->getElementsByTagName('scos');

        if (!empty($domnodelist->length)) {

            $innernodelist = $domnodelist->item(0)->getElementsByTagName('sco');

            if (!empty($innernodelist->length)) {

                for ($i = 0; $i < $innernodelist->length; $i++) {

                    if ($innernodelist->item($i)->hasAttributes()) {

                        $domnode = $innernodelist->item($i)->attributes->getNamedItem('sco-id');

                        if (!is_null($domnode)) {

                            $key = (int) $domnode->nodeValue;

                            $meetingdetail = $innernodelist->item($i);

                            $value = (!is_null($meetingdetail->getElementsByTagName('name'))) ?
                                    $meetingdetail->getElementsByTagName('name')->item(0)->nodeValue : '';

                            if (!isset($matches[$key])) {
                                $matches[$key] = new stdClass();
                            }

                            $matches[$key]->name = (string) $value;

                            $value = (!is_null($meetingdetail->getElementsByTagName('url-path'))) ?
                                    $meetingdetail->getElementsByTagName('url-path')->item(0)->nodeValue : '';

                            $matches[$key]->url = (string) $value;

                            $matches[$key]->scoid = (int) $key;

                            $value = (!is_null($meetingdetail->getElementsByTagName('date-begin'))) ?
                                    $meetingdetail->getElementsByTagName('date-begin')->item(0)->nodeValue : '';

                            $matches[$key]->starttime = (string) $value;

                            $value = (!is_null($meetingdetail->getElementsByTagName('date-end'))) ?
                                    $meetingdetail->getElementsByTagName('date-end')->item(0)->nodeValue : '';

                            $matches[$key]->endtime = (string) $value;

                        }

                    }
                }
            }
        } else {
            return false;
        }

    } else {
        return false;
    }

    return $matches;
}

/**
 * Parse XML and returns the user's principal-id
 *
 * @param string $xml XML returned from call to principal-list
 * @param mixed user's principal-id or false
 */
function aconnect_get_user_principal_id($xml) {
    $usrprincipalid = false;

    $dom = new DomDocument();
    $dom->loadXML($xml);

    $domnodelist = $dom->getElementsByTagName('principal-list');

    if (!empty($domnodelist->length)) {
        $domnodelist = $domnodelist->item(0)->getElementsByTagName('principal');

        if (!empty($domnodelist->length)) {
            if ($domnodelist->item(0)->hasAttributes()) {
                $domnode = $domnodelist->item(0)->attributes->getNamedItem('principal-id');

                if (!is_null($domnode)) {
                    $usrprincipalid = (int) $domnode->nodeValue;
                }
            }
        }
    }

    return $usrprincipalid;
}

/**
 * Check to see if a user exists on the Adobe connect server
 * searching by username
 *
 * @param object $aconnect a connection_class object
 * @param object $userdata an object with username as a property
 * @return mixed user's principal-id of match is found || false if not
 * found or error occured
 */
function aconnect_user_exists($aconnect, $usrdata) {
    global $CFG;
    $params = array(
            'action' => 'principal-list',
            'filter-login' => $usrdata->username,
        //            'filter-type' => 'meeting',
        // add more filters if this process begins to get slow
    );
    $configs = get_config('mod_adobeconnect');
    $aconnect->create_request($params);
    $group_name = $configs->offline_group;
    if ($aconnect->call_success('aconnect_user_exists')) {
        $pid = aconnect_get_user_principal_id($aconnect->_xmlresponse);
        $group = aconnect_get_groups($aconnect, $group_name);
        if ($group && $pid) {
            $res = aconnect_assing_group($aconnect, $group, $pid, true);
        }

        return $pid;
    } else {

        return false;
    }

}

function aconnect_delete_user($aconnect, $principalid = 0) {

    if (empty($principalid)) {
        return false;
    }

    $params = array(
            'action' => 'principals-delete',
            'principal-id' => $principalid,
    );

    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_delete_user')) {
        return true;
    } else {
        return false;
    }

}

/**
 * Creates a new user on the Adobe Connect server.
 * Parses XML from a principal-update call and returns
 * the principal-id of the new user.
 *
 * @param object $aconnet a connect_class object
 * @param object $usrdata an object with firstname,lastname,
 * username and email properties.
 * @return mixed principal-id of the new user or false
 */
function aconnect_create_user($aconnect, $usrdata) {
    global $CFG;
    $principal_id = false;
    $configs = get_config('mod_adobeconnect');
    $params = array(
            'action' => 'principal-update',
            'first-name' => $usrdata->firstname,
            'last-name' => $usrdata->lastname,
            'login' => $usrdata->username,
            'password' => strtoupper(md5($usrdata->username . time())),
            'extlogin' => $usrdata->username,
            'type' => 'user',
            'send-email' => 'false',
            'has-children' => 0,
            'email' => $usrdata->email,
    );

    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_create_user')) {
        $dom = new DomDocument();
        $dom->loadXML($aconnect->_xmlresponse);

        $domnodelist = $dom->getElementsByTagName('principal');

        if (!empty($domnodelist->length)) {
            if ($domnodelist->item(0)->hasAttributes()) {
                $domnode = $domnodelist->item(0)->attributes->getNamedItem('principal-id');

                if (!is_null($domnode)) {
                    $principal_id = (int) $domnode->nodeValue;
                }
            }
        }
    }
    $group_name = $configs->offline_group;
    $group = aconnect_get_groups($aconnect, $group_name);
    if ($group && $principal_id) {
        $res = aconnect_assing_group($aconnect, $group, $principal_id, true);
    }

    return $principal_id;
}

function aconnect_assign_user_perm($aconnect, $usrprincipal, $meetingscoid, $type) {
    $params = array(
            'action' => 'permissions-update',
            'acl-id' => $meetingscoid, //sco-id of meeting || principal id of user 11209,
            'permission-id' => $type, //  host, mini-host, view
            'principal-id' => $usrprincipal, // principal id of user you are looking at
    );

    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_assign_user_perm')) {
        return true;
        //        print_object($aconnect->_xmlresponse);
    } else {
        return false;
        //        print_object($aconnect->_xmlresponse);
    }
}

function aconnect_remove_user_perm($aconnect, $usrprincipal, $meetingscoid) {
    $params = array(
            'action' => 'permissions-update',
            'acl-id' => $meetingscoid, //sco-id of meeting || principal id of user 11209,
            'permission-id' => ADOBE_REMOVE_ROLE, //  host, mini-host, view
            'principal-id' => $usrprincipal, // principal id of user you are looking at
    );

    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_remove_user_perm')) {
        //        print_object($aconnect->_xmlresponse);
    } else {
        //        print_object($aconnect->_xmlresponse);
    }

}

/**
 * Check if a user has a permission
 *
 * @param object $aconnect a connect_class object
 * @param int $usrprincipal user principal-id
 * @param int $meetingscoid meeting sco-id
 * @param int $roletype can be ADOBE_PRESENTER, ADOBE_PARTICIPANT or ADOBE_REMOVE
 * @param bool $assign set to true if you want to assign the user the role type
 * set to false to just check the user's permission.  $assign parameter is ignored
 * if $roletype is ADOBE_REMOVE
 * @return TODO
 *
 */
function aconnect_check_user_perm($aconnect, $usrprincipal, $meetingscoid, $roletype, $assign = false) {
    $perm_type = '';
    $hasperm = false;

    switch ($roletype) {
        case ADOBE_PRESENTER:
            $perm_type = ADOBE_MINIADMIN_ROLE;
            break;
        case ADOBE_PARTICIPANT:
            $perm_type = ADOBE_VIEW_ROLE;
            break;
        case ADOBE_HOST:
            $perm_type = ADOBE_HOST_ROLE;
            break;
        case ADOBE_REMOVE:
            $perm_type = ADOBE_REMOVE_ROLE;
            break;
        default:
            break;
    }

    $params = array(
            'action' => 'permissions-info',
        //  'filter-permission-id' => 'mini-host',
            'acl-id' => $meetingscoid, //sco-id of meeting || principal id of user 11209,
        //        'filter-permission-id' => $perm_type, //  host, mini-host, view
            'filter-principal-id' => $usrprincipal, // principal id of user you are looking at
    );

    if (ADOBE_REMOVE_ROLE != $perm_type) {
        $params['filter-permission-id'] = $perm_type;
    }
    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_check_user_perm')) {
        $dom = new DomDocument();
        $dom->loadXML($aconnect->_xmlresponse);

        $domnodelist = $dom->getElementsByTagName('permissions');

        if (!empty($domnodelist->length)) {
            $domnodelist = $domnodelist->item(0)->getElementsByTagName('principal');

            if (!empty($domnodelist->length)) {
                $hasperm = true;
            }
        }

        if (ADOBE_REMOVE_ROLE != $perm_type and $assign and !$hasperm) {
            // TODO: check return values of the two functions below
            // Assign permission to user
            return aconnect_assign_user_perm($aconnect, $usrprincipal, $meetingscoid, $perm_type);
        } else if (ADOBE_REMOVE_ROLE == $perm_type) {
            // Remove user's permission
            return aconnect_remove_user_perm($aconnect, $usrprincipal, $meetingscoid);
        } else {
            return $hasperm;
        }
    }
}

function aconnect_get_groups($aconnect, $group_name, $assign = false) {
    $perm_type = '';
    $hasperm = false;

    $params = array(
            'action' => 'principal-list',
            'filter-type' => 'group', //sco-id of meeting || principal id of user 11209,
    );

    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_get_groups')) {
        $dom = new DomDocument();
        $dom->loadXML($aconnect->_xmlresponse);
        $xmlObject = new SimpleXMLElement($aconnect->_xmlresponse);

        $domnodelist = $dom->getElementsByTagName('principal-list');

        $innernodelist = $domnodelist->item(0)->getElementsByTagName('principal');

        if (!empty($innernodelist->length)) {

            for ($x = 0; $x < $innernodelist->length; $x++) {

                $meetingdetail = $innernodelist->item($x);
                $value = (!is_null($meetingdetail->getElementsByTagName('url-path'))) ?
                        $meetingdetail->getElementsByTagName('name')->item(0)->nodeValue : '';
                if ($value == $group_name) {
                    return $meetingdetail->attributes->getNamedItem('principal-id')->nodeValue;
                }

            }
        }

    }
    return null;
}

function aconnect_assing_group($aconnect, $group_id, $principal_id, $assign = false) {

    $params = array(
            'action' => 'group-membership-update',
            'group-id' => $group_id,
            'principal-id' => $principal_id,
            'is-member' => $assign, //sco-id of meeting || principal id of user 11209,
    );
    //echo "#PRINT";
    $res = false;
    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_assing_group')) {
        $dom = new DomDocument();
        $dom->loadXML($aconnect->_xmlresponse);
        $xmlObject = new SimpleXMLElement($aconnect->_xmlresponse);

        $domnodelist = $dom->getElementsByTagName('status');

        $res = $domnodelist->item(0)->attributes->getNamedItem('code')->nodeValue;

    }
    return $res;
}

/**
 * Remove a meeting
 *
 * @param obj $aconnect adobe connection object
 * @param int $scoid sco-id of the meeting
 * @return bool true of success false on failure
 */
function aconnect_remove_meeting($aconnect, $scoid) {
    $params = array(
            'action' => 'sco-delete',
            'sco-id' => $scoid,
    );

    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_remove_meeting')) {
        return true;
    } else {
        return false;
    }
}

/**
 * Move SCOs to the shared content folder
 *
 * @param obj $aconnect a connect_class object
 * @param array sco-ids as array keys
 * @return bool false if error or nothing to move true if a move occured
 */
function aconnect_move_to_shared($aconnect, $scolist) {
    // Get shared folder sco-id
    $shscoid = aconnect_get_folder($aconnect, 'content');

    // Iterate through list of sco and move them all to the shared folder
    if (!empty($shscoid)) {

        foreach ($scolist as $scoid => $data) {
            $params = array(
                    'action' => 'sco-move',
                    'folder-id' => $shscoid,
                    'sco-id' => $scoid,
            );

            $aconnect->create_request($params);

        }

        return true;
    } else {
        return false;
    }
}

/**
 * Gets a list of roles that this user can assign in this context
 *
 * @param object $context the context.
 * @param int $rolenamedisplay the type of role name to display. One of the
 *      ROLENAME_X constants. Default ROLENAME_ALIAS.
 * @param bool $withusercounts if true, count the number of users with each role.
 * @param integer|object $user A user id or object. By default (null) checks the permissions of the current user.
 * @return array if $withusercounts is false, then an array $roleid => $rolename.
 *      if $withusercounts is true, returns a list of three arrays,
 *      $rolenames, $rolecounts, and $nameswithcounts.
 */
function adobeconnect_get_assignable_roles($context, $rolenamedisplay = ROLENAME_ALIAS, $withusercounts = false, $user = null) {
    global $USER, $DB;

    // make sure there is a real user specified
    if ($user === null) {
        $userid = !empty($USER->id) ? $USER->id : 0;
    } else {
        $userid = !empty($user->id) ? $user->id : $user;
    }

    if (!has_capability('moodle/role:assign', $context, $userid)) {
        if ($withusercounts) {
            return array(array(), array(), array());
        } else {
            return array();
        }
    }

    $parents = $context->get_parent_context_ids(true);
    $contexts = implode(',', $parents);

    $params = array();
    $extrafields = '';
    if ($rolenamedisplay == ROLENAME_ORIGINALANDSHORT or $rolenamedisplay == ROLENAME_SHORT) {
        $extrafields .= ', r.shortname';
    }

    if ($withusercounts) {
        $extrafields = ', (SELECT count(u.id)
                             FROM {role_assignments} cra JOIN {user} u ON cra.userid = u.id
                            WHERE cra.roleid = r.id AND cra.contextid = :conid AND u.deleted = 0
                          ) AS usercount';
        $params['conid'] = $context->id;
    }

    if (is_siteadmin($userid)) {
        // show all roles allowed in this context to admins
        $assignrestriction = "";
    } else {
        $assignrestriction = "JOIN (SELECT DISTINCT raa.allowassign AS id
                                      FROM {role_allow_assign} raa
                                      JOIN {role_assignments} ra ON ra.roleid = raa.roleid
                                     WHERE ra.userid = :userid AND ra.contextid IN ($contexts)
                                   ) ar ON ar.id = r.id";
        $params['userid'] = $userid;
    }
    $params['contextlevel'] = $context->contextlevel;
    $sql = "SELECT r.id, r.name $extrafields
              FROM {role} r
              $assignrestriction
              JOIN {role_context_levels} rcl ON r.id = rcl.roleid
             WHERE rcl.contextlevel = :contextlevel
          ORDER BY r.sortorder ASC";
    $roles = $DB->get_records_sql($sql, $params);

    // Only include Adobe Connect roles
    $param = array('shortname' => 'adobeconnectpresenter');
    $presenterid = $DB->get_field('role', 'id', $param);

    $param = array('shortname' => 'adobeconnectparticipant');
    $participantid = $DB->get_field('role', 'id', $param);

    $param = array('shortname' => 'adobeconnecthost');
    $hostid = $DB->get_field('role', 'id', $param);

    foreach ($roles as $key => $data) {
        if ($key != $participantid and $key != $presenterid and $key != $hostid) {
            unset($roles[$key]);
        }
    }

    $rolenames = array();
    foreach ($roles as $role) {
        if ($rolenamedisplay == ROLENAME_SHORT) {
            $rolenames[$role->id] = $role->shortname;
            continue;
        }
        $rolenames[$role->id] = $role->name;
        if ($rolenamedisplay == ROLENAME_ORIGINALANDSHORT) {
            $rolenames[$role->id] .= ' (' . $role->shortname . ')';
        }
    }
    if ($rolenamedisplay != ROLENAME_ORIGINALANDSHORT and $rolenamedisplay != ROLENAME_SHORT) {
        $rolenames = role_fix_names($rolenames, $context, $rolenamedisplay);
    }

    if (!$withusercounts) {
        return $rolenames;
    }

    $rolecounts = array();
    $nameswithcounts = array();
    foreach ($roles as $role) {
        $nameswithcounts[$role->id] = $rolenames[$role->id] . ' (' . $roles[$role->id]->usercount . ')';
        $rolecounts[$role->id] = $roles[$role->id]->usercount;
    }
    return array($rolenames, $rolecounts, $nameswithcounts);
}

/**
 * This function accepts a username and an email and returns the user's
 * adobe connect user name, depending on the module's configuration settings
 *
 * @param string - moodle username
 * @param string - moodle email
 *
 * @return string - user's adobe connect user name
 */
function set_username($username, $email) {
    global $CFG;
    $configs = get_config('mod_adobeconnect');
    if (isset($configs->email_login) and !empty($configs->email_login)) {
        return $email;
    } else {
        return $username;
    }
}

/**
 * This function search through the user-meetings folder for a folder named
 * after the user's login name and returns the sco-id of the user's folder
 *
 * @param obj - adobe connection connection object
 * @param string - the name of the user's folder
 * @return mixed - sco-id of the user folder (int) or false if no folder exists
 *
 */
function aconnect_get_user_folder_sco_id($aconnect, $folder_name) {

    $scoid = false;
    $usr_meet_scoid = aconnect_get_folder($aconnect, 'user-meetings');

    if (empty($usr_meet_scoid)) {
        return $scoid;
    }

    $params = array('action' => 'sco-expanded-contents',
            'sco-id' => $usr_meet_scoid,
            'filter-name' => $folder_name);

    $aconnect->create_request($params);

    if ($aconnect->call_success('aconnect_get_user_folder_sco_id')) {

        $dom = new DomDocument();
        $dom->loadXML($aconnect->_xmlresponse);

        $domnodelist = $dom->getElementsByTagName('sco');

        if (!empty($domnodelist->length)) {
            if ($domnodelist->item(0)->hasAttributes()) {
                $domnode = $domnodelist->item(0)->attributes->getNamedItem('sco-id');

                if (!is_null($domnode)) {
                    $scoid = (int) $domnode->nodeValue;
                }
            }
        }
    }

    return $scoid;
}

/**
 * This function returns the user's adobe connect login username based off of
 * the adobe connect module's login configuration settings (Moodle username or
 * Moodle email)
 *
 * @param int userid
 * @return mixed - user's login username or false if something bad happened
 */
function get_connect_username($userid) {
    global $DB;

    $username = '';
    $param = array('id' => $userid);
    $record = $DB->get_record('user', $param, 'id,username,email');

    if (!empty($userid) && !empty($record)) {
        $username = set_username($record->username, $record->email);
    }

    return $username;
}

function getRecordings($context, $instanceid, $user) {
    global $DB, $USER;

    if (!has_capability('mod/adobeconnect:viewrecordings', $context, $user->id)) {
        return array('status' => 0, 'is_notification' => 0, 'msg' => "you don't have permission for this action", 'err_msg' => '',
                'data' => []);
    }

    try {
        $recordings = $DB->get_records('adobeconnect_recordings', ['instanceid' => $instanceid], 'start_date DESC');
        if (!$recordings) {
            return array(
                    'status' => 0,
                    'is_notification' => 0,
                    'msg' => get_string('offline_server_err_sco', 'adobeconnect'),
                    'data' => array()
            );
        }
        $scoids = array_map(function($item) {
            return $item->recordingscoid;
        }, $recordings);
        $scoids = array_values($scoids);
        $manager = false;
        if (has_capability('mod/adobeconnect:managerecordings', $context, $USER->id)) {
            $manager = true;
        }
        $offline_recordings = getOfflineRecordings($scoids, $instanceid, $manager);
        $adobe_offline = false;
        $err_msg = "";
        $status = $offline_recordings['status'];
        if ($offline_recordings['status'] == 1) {
            try {
                $urls = json_decode($offline_recordings['data']);
                $offline_recordings['data'] = $urls->data? $urls->data[0] : [];
            } catch (Exception $exception) {
                debugging("error getting offline recordings" . $exception, DEBUG_DEVELOPER);
            }
        } else if ($offline_recordings['status'] == -1) {
            $adobe_offline = true;
        } else {
            $err_msg = $offline_recordings['msg'];
        }

        $array_records = array_map(function($record) use ($USER, $offline_recordings, $adobe_offline) {
            $scoid = $record->recordingscoid;
            $url_offline = null;
            $in_queue = false;
            $in_server = false;

            if ($offline_recordings['status'] == 1) {
                $url_obj = $offline_recordings['data'];
                if ($url_obj->$scoid) {
                    $of_record = $url_obj->$scoid;
                    if ($of_record->in_server === true){
                        $in_server = true;
                    }
                    if ($of_record->in_queue === true){
                        $in_queue = true;
                    }
                    if ($of_record->downloaded === true && $of_record->file_exist === true) {
                        $url_offline = $of_record->url;
                    }
                }
            }

            $record->adobe_offline = $adobe_offline;
            $record->formated_create_date = userdate($record->create_date, get_string('strftimedaydatetime'));
            $record->formated_duration = secondsTooTime($record->duration);
            $record->url_offline = $url_offline;
            $record->in_offline_queue = $in_queue;
            $record->in_offline_server = $in_server;
            $record->sesskey = $USER->sesskey;
            $record->hiderow = $record->hideonline && $record->hideoffline;
            return $record;
        }
                , $recordings);

        return array('status' => 1, 'is_notification' => 0, 'msg' => 'Success', 'err_msg' => $err_msg,
                'data' => $array_records);

    } catch (Exception $e) {
        debugging("error getting recordings" . $e, DEBUG_DEVELOPER);
        return array('status' => 0, 'is_notification' => 0, 'msg' => $e->getMessage(), 'err_msg' => '', 'data' => []);
    }

}

function syncRecordings($meetscoids, $cmid, $groupmode, $usrprincipal, $isAuto) {
    global $DB, $USER;
    $context = context_module::instance($cmid);
    if (!has_capability('mod/adobeconnect:viewrecordings', $context, $USER->id)) {
        return array('status' => 0, 'is_notification' => 0, 'msg' => "you don't have permission for this action", 'data' => '');
    }
    $cm = get_coursemodule_from_id('adobeconnect', $cmid);
    if (!$isAuto) {
        if (!has_capability('mod/adobeconnect:managerecordings', $context, $USER->id)) {
            $msg = get_string('permissiondo', 'adobeconnect',
                    get_string('sync_recordings', 'adobeconnect'));
            return array('status' => -1, 'is_notification' => 0, 'msg' => $msg, 'data' => '');
        }
    } else {
        $ins = $DB->get_record('adobeconnect', ['id' => $cm->instance]);
        if ((time() - $ins->last_sync_record) < 3600) {
            //$recordings_db = getRecordings($context, $cmid, $USER);
            //$data = new stdClass();
            //$data->records = array_values($recordings_db);
            //$data->canmanagerecordings = true;
            //$data->candeleterecordings = false;
            //if (has_capability('mod/adobeconnect:deleterecordings', $context, $USER->id)) {
            //    $data->candeleterecordings = true;
            //}
            return array('status' => 0, 'is_notification' => 0, 'msg' => "NO_SYNC", 'data' => json_encode([]));

        }
    }

    cache_helper::purge_by_event('clearRecCache');
    if (!empty($meetscoids)) {
        $recscoids = array();
        $recordings = array();
        $aconnect = aconnect_login();
        $fldid = aconnect_get_folder($aconnect, 'forced-archives');
        // Get the forced recordings folder sco-id
        // Get recordings that are based off of the meeting

        foreach ($meetscoids as $scoid) {
            $data = aconnect_get_recordings($aconnect, $fldid, $scoid->meetingscoid);

            if (!empty($data)) {
                // Store recordings in an array to be moved to the Adobe shared folder later on
                $recscoids = array_merge($recscoids, array_keys($data));

            }

        }

        // Move the meetings to the shared content folder
        if (!empty($recscoids)) {
            $recscoids = array_flip($recscoids);

            if (aconnect_move_to_shared($aconnect, $recscoids)) {
                // do nothing
            }
        }
        //Get the shared content folder sco-id
        // Create a list of recordings moved to the shared content folder
        $fldid = aconnect_get_folder($aconnect, 'content');
        foreach ($meetscoids as $scoid) {
            // May need this later on
            $data = aconnect_get_recordings($aconnect, $fldid, $scoid->meetingscoid);

            if (!empty($data)) {
                $recordings[] = $data;
            }

            $data2 = aconnect_get_recordings($aconnect, $scoid->meetingscoid, $scoid->meetingscoid);

            if (!empty($data2)) {
                $recordings[] = $data2;
            }

        }
        // Clean up any duplciated meeting recordings.  Duplicated meeting recordings happen when the
        // recording settings on ACP server change between "publishing recording links in meeting folders" and
        // not "publishing recording links in meeting folders"
        $names = array();
        foreach ($recordings as $key => $recordingarray) {
            foreach ($recordingarray as $key2 => $record) {


                if (!empty($names)) {

                    if (!array_search($record->name, $names)) {

                        $names[] = $record->name;
                    } else {

                        unset($recordings[$key][$key2]);
                    }
                } else {

                    $names[] = $record->name;
                }
            }
        }

        unset($names);
        // Check the user's capability and assign them view permissions to the recordings folder
        // if it's a public meeting give them permissions regardless
        if ($groupmode) {

            if (has_capability('mod/adobeconnect:meetingpresenter', $context, $USER->id) or
                    has_capability('mod/adobeconnect:meetingparticipant', $context, $USER->id)) {
                if (aconnect_assign_user_perm($aconnect, $usrprincipal, $fldid, ADOBE_VIEW_ROLE)) {
                    //DEBUG
                    // echo 'true';
                } else {
                    //DEBUG
                    debugging("error assign user recording folder permissions", DEBUG_DEVELOPER);
                    //                print_object('error assign user recording folder permissions');
                    //                print_object($aconnect->_xmlrequest);
                    //                print_object($aconnect->_xmlresponse);
                }
            }
        } else {
            aconnect_assign_user_perm($aconnect, $usrprincipal, $fldid, ADOBE_VIEW_ROLE);
        }
        aconnect_logout($aconnect);
        //var_dump($recordings);
        $recordings_json = array();
        if (count($recordings) == 0) {
            $data = new stdClass();
            try {
                $transaction = $DB->start_delegated_transaction();
                $adb = new stdClass();
                $adb->id = $cm->instance;
                $adb->last_sync_record = time();
                $DB->update_record('adobeconnect', $adb);
                $transaction->allow_commit();
                $data->last_sync = userdate($adb->last_sync_record, get_string('strftimedatetimeshort'));
            } catch (Exception $e) {
                $transaction->rollback($e);
            }
            return array('status' => -2, 'is_notification' => 0, 'msg' => "Ops successful, no records found",
                    'data' => json_encode($data));
        }

        $recording_keys = [];
        foreach ($recordings as $recording_scos) {
            $recording_keys = array_merge($recording_keys, array_keys($recording_scos));
            foreach ($recording_scos as $key => $recording) {
                $record = new stdClass();
                $record->name = $recording->name;
                $record->url = $recording->url;
                $record->instanceid = $cmid;
                $record->recordingscoid = $key;
                $record->sourcesco = $recording->sourcesco;
                $record->start_date = strtotime($recording->startdate);
                $record->end_date = strtotime($recording->enddate);
                $record->create_date = strtotime($recording->createdate);
                $record->modified = strtotime($recording->modified);
                $record->duration = $recording->duration;
                $record->hideoffline = 0;
                $record->hideonline = 0;
                $record->hiderow = 0;
                $record->deleted = 0;
                $record->sesskey = $USER->sesskey;
                $exist = $DB->record_exists('adobeconnect_recordings',
                        ['instanceid' => $cmid, 'recordingscoid' => $key]);
                if (!$exist) {
                    try {
                        $transaction = $DB->start_delegated_transaction();
                        $record->id = $DB->insert_record('adobeconnect_recordings', $record, true);
                        $transaction->allow_commit();
                    } catch (Exception $e) {
                        $transaction->rollback($e);
                    }
                }
                $recordings_json[] = $record;
            }
        }
        if ($recording_keys) {
            try {

                $transaction = $DB->start_delegated_transaction();
                $in = "(" . implode(',', $recording_keys) . ")";
                $sql = "UPDATE {adobeconnect_recordings} SET deleted = 1 WHERE instanceid = ? AND recordingscoid NOT IN $in";
                $DB->execute($sql, [$cmid]);
                $transaction->allow_commit();

            } catch (Exception $e) {
                $transaction->rollback($e);
            }
        }
        $data = new stdClass();
        $recordings_db = getRecordings($context, $cmid, $USER);
        $msg = get_string('sync_recording_success', 'adobeconnect');
        if ($recordings_db['err_msg']) {
            $msg .= "\n" . $recordings_db['err_msg'];
        }
        $data->records = array_values($recordings_db['data']);
        $data->canmanagerecordings = false;
        if (has_capability('mod/adobeconnect:managerecordings', $context, $USER->id)) {
            $data->canmanagerecordings = true;
        }
        $data->candeleterecordings = false;
        if (has_capability('mod/adobeconnect:deleterecordings', $context, $USER->id)) {
            $data->candeleterecordings = true;
        }
        try {
            $transaction = $DB->start_delegated_transaction();
            $adb = new stdClass();
            $adb->id = $cm->instance;
            $adb->last_sync_record = time();
            $DB->update_record('adobeconnect', $adb);
            $transaction->allow_commit();
            $data->last_sync = userdate($adb->last_sync_record, '%d/%m/%Y, %H:%M');
        } catch (Exception $e) {
            $transaction->rollback($e);
        }

        return array('status' => 1, 'is_notification' => 1, 'msg' => $msg, 'data' => json_encode($data));
    }

    return array('status' => 0, 'is_notification' => 0, 'msg' => "meetscoids is empty", 'data' => '');

}

function syncAttendances($meetscoids, $instanceid, $isAuto) {
    global $DB, $USER;

    $context = context_module::instance($instanceid);
    $cm = get_coursemodule_from_id('adobeconnect', $instanceid);

    if (!has_capability('mod/adobeconnect:viewattendees', $context, $USER->id)) {
        $msg = get_string('permissiondo', 'adobeconnect',
                get_string('sync_attendance', 'adobeconnect'));
        if ($isAuto) {
            return array('status' => 0, 'is_notification' => 0, 'msg' => $msg, 'data' => '');
        }
        return array('status' => -1, 'is_notification' => 0, 'msg' => $msg, 'data' => '');
    }
    if ($isAuto) {
        $ins = $DB->get_record('adobeconnect', ['id' => $cm->instance]);
        if ((time() - $ins->last_sync_attendance) < 3600) {
            //$data = new stdClass();
            //$attendances_db = getAttendances($context, $instanceid);
            //$data->rows = ($attendances_db);
            //$data->userfields = get_custom_fields();
            return array('status' => 0, 'is_notification' => 0, 'msg' => "NO_SYNC", 'data' => json_encode([]));

        }
    }

    if (!empty($meetscoids)) {
        $aconnect = aconnect_login();
        $rows = array();
        $attends = array();
        foreach ($meetscoids as $scoid) {
            $row = aconnect_get_attendance($aconnect, $scoid->meetingscoid, $scoid->meetingscoid);
            $rows = array_merge($rows, $row);
        }
        aconnect_logout($aconnect);

        if (count($rows) == 0) {
            $data = new stdClass();
            try {
                $transaction = $DB->start_delegated_transaction();
                $adb = new stdClass();
                $adb->id = $cm->instance;
                $adb->last_sync_attendance = time();
                $DB->update_record('adobeconnect', $adb);
                $transaction->allow_commit();
                $data->last_sync = userdate($adb->last_sync_attendance, '%d/%m/%Y, %H:%M');
            } catch (Exception $e) {
                $transaction->rollback($e);
            }
            return array(
                    'status' => -2,
                    'is_notification' => 0,
                    'msg' => get_string('sync_attendance_not_found', 'adobeconnect'),
                    'data' => json_encode($data));
        }

        foreach ($rows as $row) {
            $attendee = new stdClass();
            $attendee->instanceid = $instanceid;
            $attendee->email = $row->name;
            $attendee->session_name = $row->session_name;
            $attendee->sco_name = $row->sco_name;
            $attendee->participant_name = $row->participant_name;
            $attendee->attendances = array();
            $attendee->id = null;
            $attendee->id = $DB->get_field('adobeconnect_attendees', 'id',
                    ['instanceid' => $instanceid, 'email' => $attendee->email]);
            if (!$attendee->id) {
                try {
                    $transaction = $DB->start_delegated_transaction();
                    $attendee->id = $DB->insert_record('adobeconnect_attendees', $attendee, true);
                    $transaction->allow_commit();
                } catch (Exception $e) {
                    $transaction->rollback($e);
                }
            }
            try {
                $transaction = $DB->start_delegated_transaction();
                $DB->delete_records('adobeconnect_attendance', ['attendee_id' => $attendee->id]);
                $transaction->allow_commit();
            } catch (Exception $e) {
                $transaction->rollback($e);
            }
            foreach ($row->dates as $index => $attendance) {
                $attended = new stdClass();
                $attended->attendee_id = $attendee->id;
                $attended->start_date = strtotime($attendance['start_date']);
                $attended->end_date = strtotime($attendance['end_date']);
                //$row = $DB->get_record('adobeconnect_attendance',
                //        ['attendee_id' => $attendee->id,
                //                'start_date' => $attended->start_date]);
                //if (!$row->id) {
                try {
                    $transaction = $DB->start_delegated_transaction();
                    $id = $DB->insert_record('adobeconnect_attendance', $attended, true);
                    $transaction->allow_commit();
                } catch (Exception $e) {
                    $transaction->rollback($e);
                }
                //} else if ($row->end_date == "0") {
                //    echo "TEST";
                //    try {
                //        $transaction = $DB->start_delegated_transaction();
                //        $sql = "UPDATE {adobeconnect_attendance} SET end_date = ? WHERE id = ?";
                //        $DB->execute($sql, [$attended->end_date, $row->id]);
                //        $transaction->allow_commit();
                //    } catch (Exception $e) {
                //        $transaction->rollback($e);
                //    }
                //}
                $attended->start_date = userdate($attended->start_date, get_string('strftimedaydate'));

                if ($attended->end_date == "0") {
                    $attended->end_date = get_string("insession");
                } else {

                    if (compareDate($attended->start_date, $attended->end_date)) {
                        $attended->end_date = userdate($attended->end_date, get_string('strftimedaydatetime'));
                    } else {
                        $attended->end_date = userdate($attended->end_date, '%H:%M');
                    }
                }
                $attendee->userfields = get_user_custom_fields($attendee->email);
                $attendee->attendances[] = $attended;
            }

            $attends[] = $attendee;
        }
        $data = new stdClass();
        $attendances_db = getAttendances($context, $instanceid);

        $data->rows = ($attendances_db);
        $data->userfields = get_custom_fields();
        try {
            $transaction = $DB->start_delegated_transaction();
            $adb = new stdClass();
            $adb->id = $cm->instance;
            $adb->last_sync_attendance = time();
            $DB->update_record('adobeconnect', $adb);
            $transaction->allow_commit();
            $data->last_sync = userdate($adb->last_sync_attendance, get_string('strftimedatetimeshort'));
        } catch (Exception $e) {
            $transaction->rollback($e);
        }

        return array('status' => 1,
                'is_notification' => 1,
                'msg' => get_string('sync_attendance_success', 'adobeconnect'),
                'data' => json_encode($data));
    } else {
        return array('status' => 0,
                'is_notification' => 0,
                'msg' => get_string('sync_attendance_fail', 'adobeconnect'),
                'data' => '');
    }
}

/** @noinspection PhpComposerExtensionStubsInspection */
function getOfflineRecordings($meetscoids, $instanceid, $manager = false) {
    global $USER;
    $is_notification = 0;
    if ($manager) {
        $is_notification = 1;
    }
    if (!empty($meetscoids)) {

        $use = get_config('mod_adobeconnect')->use_offline;
        $host = get_config('mod_adobeconnect')->offline_host;
        $secret = get_config('mod_adobeconnect')->offline_host_secret;
        if (!$use || !$host) {
            return array('status' => -1, 'is_notification' => 0, 'msg' => "Offline server is not enabled", 'data' => '');
        }
        $cache = cache::make('mod_adobeconnect', 'recordings');
        $key = 'urls' . $instanceid;

        $result = $cache->get($key);
        if ($result) {
            if ($result == "UNAUTHORIZED ACCESS") {
                return array(
                        'status' => 0,
                        'is_notification' => $is_notification,
                        'msg' => get_string('offline_server_err_auth', 'adobeconnect'),
                        'data' => ''
                );
            }
            return array('status' => 1, 'is_notification' => 0, 'msg' => "success! data found in cache.", 'data' => $result);
        }

        $url = "{$host}/connect/rest.php?secret={$secret}";
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('action' => 'get', 'scoid' => json_encode($meetscoids)));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $info = curl_getinfo($ch);

            if (empty($info['http_code']) || $info['http_code'] != 200) {
                return array(
                        'status' => 0,
                        'is_notification' => $is_notification,
                        'msg' => get_string('offline_server_err_reach', 'adobeconnect'),
                        'data' => ''
                );
            }

            if ($response == "UNAUTHORIZED ACCESS") {
                return array(
                        'status' => 0,
                        'is_notification' => $is_notification,
                        'msg' => get_string('offline_server_err_auth', 'adobeconnect'),
                        'data' => ''
                );
            }
            $cache->set($key, $response);
            return array('status' => 1,
                    'is_notification' => 0,
                    'msg' => get_string('offline_data_received', 'adobeconnect'),
                    'data' => $response);
        } catch (Exception $e) {
            return array(
                    'status' => 0,
                    'is_notification' => $is_notification,
                    'msg' => get_string('offline_server_err_reach', 'adobeconnect'),
                    'data' => ''
            );
        }

    } else {
        return array(
                'status' => 0,
                'is_notification' => 0,
                'msg' => get_string('offline_server_err_sco', 'adobeconnect'),
                'data' => ''
        );
    }
}
function addToOfflineQueue($cmid,$scoid,$recording_id) {
    global $DB,$USER;
    $context = context_module::instance($cmid);
    $isNotification = false;
    if (has_capability('mod/adobeconnect:managerecordings', $context, $USER->id)) {
        $isNotification = true;
    }
    if (!empty($scoid)) {
        $use = get_config('mod_adobeconnect')->use_offline;
        if (!$use) {
            return array('status' => -1, 'is_notification' => $isNotification, 'msg' => "Offline server is not enabled", 'data' => '');
        }
        $host = get_config('mod_adobeconnect')->offline_host;
        $secret = get_config('mod_adobeconnect')->offline_host_secret;
        $url = "{$host}/connect/rest.php?secret={$secret}";
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('action' => 'make_offline', 'scoid' => $scoid));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $info = curl_getinfo($ch);

            if (empty($info['http_code']) || $info['http_code'] != 200) {
                return array(
                    'status' => 0,
                    'is_notification' => $isNotification,
                    'msg' => get_string('offline_server_err_reach', 'adobeconnect'),
                    'data' => ''
                );
            }

            if ($response == "UNAUTHORIZED ACCESS") {
                return array(
                    'status' => 0,
                    'is_notification' => $isNotification,
                    'msg' => get_string('offline_server_err_auth', 'adobeconnect'),
                    'data' => ''
                );
            }
            try {
                $res = json_decode($response);
                return array('status' => 1, 'is_notification' => $isNotification, 'msg' => $res->message ?: '', 'data' => $response);
            } catch (Exception $exception) {
                return array('status' => 1, 'is_notification' => $isNotification, 'msg' => $exception->getMessage(), 'data' => $response);
            }

        } catch (Exception $e) {
            return array(
                'status' => 0,
                'is_notification' => $isNotification,
                'msg' => get_string('offline_server_err_reach', 'adobeconnect'),
                'data' => ''
            );
        }

    } else {
        return array(
            'status' => 0,
            'is_notification' => $isNotification,
            'msg' => get_string('offline_server_err_sco', 'adobeconnect'),
            'data' => ''
        );
    }
}

function deleteOfflineRecordings($scoid) {
    if (!empty($scoid)) {
        $use = get_config('mod_adobeconnect')->use_offline;
        if (!$use) {
            return array('status' => -1, 'is_notification' => 0, 'msg' => "Offline server is not enabled", 'data' => '');
        }
        $host = get_config('mod_adobeconnect')->offline_host;
        $secret = get_config('mod_adobeconnect')->offline_host_secret;
        $url = "{$host}/connect/rest.php?secret={$secret}";
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('action' => 'delete', 'scoid' => $scoid));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            try {
                $res = json_decode($response);
                return array('status' => 1, 'is_notification' => 0, 'msg' => $res->message, 'data' => $response);
            } catch (Exception $exception) {
                return array('status' => 1, 'is_notification' => 0, 'msg' => $exception->getMessage(), 'data' => $response);
            }

        } catch (Exception $e) {
            return array(
                    'status' => 0,
                    'is_notification' => 0,
                    'msg' => get_string('offline_server_err_reach', 'adobeconnect'),
                    'data' => ''
            );
        }

    } else {
        return array(
                'status' => 0,
                'is_notification' => 0,
                'msg' => get_string('offline_server_err_sco', 'adobeconnect'),
                'data' => ''
        );
    }
}

function deleteRecording($cmid, $recording_scoid, $recording_id) {
    global $USER, $DB;

    $context = context_module::instance($cmid);
    if (!has_capability('mod/adobeconnect:deleterecordings', $context, $USER->id)) {
        $msg = get_string('permissiondo', 'adobeconnect',
                get_string('delete_recording', 'adobeconnect'));
        return array('status' => -1, 'is_notification' => 1, 'msg' => $msg, 'data' => '');
    }
    $aconnect = aconnect_login();
    //$fldid = aconnect_get_folder($aconnect, 'forced-archives');
    //$recording_scoid = optional_param('recording_scoid', null, PARAM_SEQUENCE);
    if ($recording_scoid) {
        $res = aconnect_delete_recordings($aconnect, $recording_scoid);
        aconnect_logout($aconnect);

        try {
            $transaction = $DB->start_delegated_transaction();
            $DB->delete_records('adobeconnect_recordings', ['id' => $recording_id]);
            $transaction->allow_commit();
        } catch (Exception $e) {
            $msg = get_string('removed_error_db', 'adobeconnect');
            return array(
                    'status' => 0,
                    'is_notification' => 1,
                    'msg' => $msg,
                    'data' => ''
            );
            $transaction->rollback($e);
        }
        if ($res['status']) {
            $data = deleteOfflineRecordings($recording_scoid);
            if ($data != -1) {
                $msg = get_string('offline_server', 'adobeconnect');
                $msg .= "\n" . htmlspecialchars($data['msg']);
            }
            $msg = get_string('removed_recording', 'adobeconnect') . "\n" . $msg;
            return array(
                    'status' => 1,
                    'is_notification' => 1,
                    'msg' => $msg,
                    'data' => ''
            );
        } else {
            $msg = get_string('removed_error_adobe', 'adobeconnect');
            return array(
                    'status' => 1,
                    'is_notification' => 1,
                    'msg' => $msg,
                    'data' => ''
            );
        }
        return $res;
    }
    aconnect_logout($aconnect);
    return array(
            'status' => 0,
            'is_notification' => 0,
            'msg' => 'recording not found',
            'data' => ''
    );

}

function recordingHideShowOnline($cmid, $recordingId, $hide = 0) {
    global $USER, $DB;
    $context = context_module::instance($cmid);
    if (!has_capability('mod/adobeconnect:managerecordings', $context, $USER->id)) {
        return array(
                'status' => 0,
                'is_notification' => 0,
                'msg' => 'you don\'t have permission',
                'data' => ''
        );
    }
    try {
        $transaction = $DB->start_delegated_transaction();
        $DB->set_field('adobeconnect_recordings', 'hideonline', $hide, ['id' => $recordingId]);
        $transaction->allow_commit();
        return array(
                'status' => 1,
                'is_notification' => 0,
                'msg' => 'status for show/hide online link changed successfully',
                'data' => ''
        );

    } catch (Exception $e) {
        $transaction->rollback($e);
    }
    return array(
            'status' => 0,
            'is_notification' => 0,
            'msg' => 'database error',
            'data' => ''
    );
}

function recordingHideShowOffline($cmid, $recordingId, $hide = 0) {
    global $USER, $DB;
    $context = context_module::instance($cmid);
    if (!has_capability('mod/adobeconnect:managerecordings', $context, $USER->id)) {
        return array(
                'status' => 0,
                'is_notification' => 0,
                'msg' => 'you don\'t have permission',
                'data' => ''
        );
    }
    try {
        $transaction = $DB->start_delegated_transaction();
        $DB->set_field('adobeconnect_recordings', 'hideoffline', $hide, ['id' => $recordingId]);
        $transaction->allow_commit();
        return array(
                'status' => 1,
                'is_notification' => 0,
                'msg' => 'status for show/hide offline link changed successfully',
                'data' => ''
        );

    } catch (Exception $e) {
        $transaction->rollback($e);
    }
    return array(
            'status' => 0,
            'is_notification' => 0,
            'msg' => 'database error',
            'data' => ''
    );
}

function recordingHideShowRow($cmid, $recordingId, $hide = 0) {
    global $USER, $DB;
    $context = context_module::instance($cmid);
    if (!has_capability('mod/adobeconnect:managerecordings', $context, $USER->id)) {
        return array(
                'status' => 0,
                'is_notification' => 0,
                'msg' => 'you don\'t have permission',
                'data' => ''
        );
    }
    try {
        $transaction = $DB->start_delegated_transaction();
        $DB->set_field('adobeconnect_recordings', 'hideonline', $hide, ['id' => $recordingId]);
        $DB->set_field('adobeconnect_recordings', 'hideoffline', $hide, ['id' => $recordingId]);
        $transaction->allow_commit();
        return array(
                'status' => 1,
                'is_notification' => 0,
                'msg' => 'status for show/hide recording changed successfully',
                'data' => ''
        );

    } catch (Exception $e) {
        $transaction->rollback($e);
    }
    return array(
            'status' => 0,
            'is_notification' => 0,
            'msg' => 'database error',
            'data' => ''
    );
}

function getAttendances($context, $instanceid) {
    global $DB, $USER;
    $configs = get_config('mod_adobeconnect');
    $canViewAttendances = has_capability('mod/adobeconnect:viewattendees', $context, $USER->id);

    if (!$canViewAttendances && !$configs->view_own_attendance) {
        return [];
    }
    try {
        //$attendees = $DB->get_records('adobeconnect_attendeess', ['instanceid' => $instanceid], 'email ASC');
        $sql = "SELECT * FROM {adobeconnect_attendees} attendees
                  JOIN {adobeconnect_attendance} attendance
                  ON attendees.id = attendance.attendee_id
                 WHERE  instanceid = ?";
        if (!$canViewAttendances && $configs->view_own_attendance) {
            $sql .= " AND attendees.email = ?";
        }

        $sql .= " ORDER BY attendees.email,attendance.start_date DESC";
        $attendees = $DB->get_records_sql($sql, [$instanceid,$USER->email]);
        $cfield = get_custom_fields();

        $attendees = toNestedObject($attendees);
        foreach ($attendees as $key => $attendee) {
//            if ((!$canViewAttendances && $configs->view_own_attendance)
//                && html_entity_decode($attendee->email) != html_entity_decode($USER->email)) {
//                unset($key,$attendees);
//                continue;
//            }
            $attendee->email = html_entity_decode($attendee->email);
            $attendee->session_name = html_entity_decode($attendee->session_name);
            $attendee->participant_name = html_entity_decode($attendee->participant_name);
            $attendee->user_fields = get_user_custom_fields($attendee->email);
            $attendee->duration = 0;
            if (!$attendee->user_fields && $cfield) {
                $attendee->user_fields = [];
                foreach ($cfield as $cf) {
                    array_push($attendee->user_fields, "-");
                }
            }
            $start_dates = array();
            $join_dates = array();
            $join_times = array();
            $exit_times = array();
            $end_dates = array();

            foreach ($attendee->attendances as $index => $attendance) {
                $sd = $attendance->start_date;
                $ed = $attendance->end_date;
                $start_dates[] = convertDate($sd);
                $join_dates[] = convertDate($sd, 'strftimedaydate');
                $join_times[] = userdate($sd, "%H:%M");
                $exit_times[] = ($ed < $sd) ? "-" : userdate($ed, "%H:%M");

                $attendee->use_hour = true;
                if (!compareDate($sd, $ed)) {
                    $attendee->use_hour = false;
                    $end_dates[] = ($ed < $sd) ? "-" : convertDate($ed);
                } else {
                    $end_dates[] = ($ed < $sd) ? "-" : convertDate($ed, '%H:%M', false);
                }
                if ($ed > $sd) {
                    $attendee->duration += $ed - $sd;
                }
            }
            $attendee->formated_duration = secondsTooTime($attendee->duration);
            $attendee->log_count = count($attendee->attendances);

            $attendee->start_dates = $start_dates;
            $attendee->end_dates = $end_dates;
            $attendee->join_dates = $join_dates;

            $attendee->join_times = $join_times;
            $attendee->exit_times = $exit_times;

        }

        return array_values($attendees ?? []);
    } catch (Exception $e) {
        debugging("error getting attendees" . $e, DEBUG_DEVELOPER);
        return [];
    }

}

function convertDate($time, $format = 'strftimedaydatetime', $useString = true) {
    $timestamp = ($time);
    //return $date;
    if ($useString) {
        return userdate($timestamp, get_string($format));
    } else {
        return userdate($timestamp, $format);
    }
}

function toNestedObject($data) {
    $output = array();

    foreach ($data as $key => $datum) {
        $obj = clone($datum);
        unset($obj->start_date);
        unset($obj->end_date);
        unset($obj->attendee_id);
        $attendance = new stdClass();
        $attendance->start_date = $datum->start_date;
        $attendance->end_date = $datum->end_date;
        if (array_key_exists($datum->email, $output)) {
            $output[$datum->email]->attendances[] = $attendance;
        } else {
            $obj->attendances[] = $attendance;
            $output[$datum->email] = $obj;
        }

    }
    return $output;
}

function compareDate($firstDate, $secondDate) {
    $timestamp1 = (int)($firstDate);
    $timestamp2 = (int)($secondDate);
    $date1 = date('Y-m-d', $timestamp1);
    $date2 = date('Y-m-d', $timestamp2);
    return ($date1 == $date2);

}

function secondsTooTime($raw) {
    $seconds = floor($raw % (60));
    $min = floor(($raw % (60 * 60)) / 60);
    $hours = floor($raw / (60 * 60));
    if ($seconds < 10) {
        $seconds = "0" . $seconds;
    }
    if ($min < 10) {
        $min = "0" . $min;
    }
    if ($hours < 10) {
        $hours = "0" . $hours;
    }
    return $hours . ":" . $min . ":" . $seconds;
}

function get_custom_fields() {

    $fields = get_config('mod_adobeconnect')->customfields;
    $fields = explode(PHP_EOL, $fields);
    $custom_fields = get_profile_fields();

    $fieldsets = [];
    foreach ($fields as $field) {
        $field = trim($field);
        if ($custom_fields && array_key_exists($field, $custom_fields)) {
            $fieldsets[] = $custom_fields[$field];
        }

    }
    return $fieldsets;
}

function get_user_custom_fields($username) {
    global $DB;
    $sql = 'SELECT id FROM {user} WHERE username = ? OR email = ?';
    $userid = $DB->get_field_sql($sql, [$username, $username]);
    $fields = get_config('mod_adobeconnect')->customfields;
    $fields = explode(PHP_EOL, $fields);
    $myuser = profile_user_record($userid);
    $fieldsets = [];
    foreach ($fields as $field) {
        $field = trim($field);

        if (!empty($myuser->$field)) {
            $obj = new stdClass();
            $obj->value = $myuser->$field;
            $obj->key = $field;

            $fieldsets[] = $obj;
        }
    }

    return $fieldsets;
}

function get_profile_fields() {
    global $DB;
    $order = $DB->sql_order_by_text('name');
    if (!$fields = $DB->get_records_menu('user_info_field', null, $order, 'shortname, name')) {
        return null;
    }

    return $fields;
}
/**
 * TEST FUNCTIONS - DELETE THIS AFTER COMPLETION OF TEST
 */
/*
function texpandsco ($aconnect, $scoid) {
    global $USER;

    $folderscoid = false;
    $params = array('action' => 'sco-expanded-contents',
                    'sco-id' => $scoid,
                    'filter-name' => $USER->email);

    $aconnect->create_request($params);

//    if ($aconnect->call_success()) {
//    }

}

function tout ($data) {
    $filename = '/tmp/tout.xml';
    $somecontent = $data;

    if (is_writable($filename)) {
        if (!$handle = fopen($filename, 'w')) {
             echo "Cannot open file ($filename)";
             return;
        }

        // Write $somecontent to our opened file.
        if (fwrite($handle, $somecontent) === FALSE) {
            echo "Cannot write to file ($filename)";
            return;
        }

        //echo "Success, wrote ($somecontent) to file ($filename)";

        fclose($handle);

    } else {
        echo "The file $filename is not writable";
    }
} */
