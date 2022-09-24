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
class mod_adobeconnect_renderer extends plugin_renderer_base
{

    /**
     * Returns HTML to display the meeting details
     *
     * @param  object  $meetingdetail
     * @param  int  $cmid
     * @param  int  $groupid
     *
     * @return string
     */
    public function display_meeting_detail($meetingdetail, $cmid, $groupid = 0)
    {
        global $CFG;

        $target = new moodle_url('/mod/adobeconnect/view.php');

        $attributes = array('method' => 'POST', 'target' => $target);

        $param = array('class' => 'url_test', 'id' => 'bstest');
        //            $html .= html_writer::tag('label', $meetingdetail->url, $param);

        $html = html_writer::start_tag('form', $attributes);

        // Display the main field set
        $param = array('class' => 'aconfldset');
        $html .= html_writer::start_tag('div', $param);

        // Display the meeting name field and value
        $param = array('class' => 'aconmeetinforow');
        $html .= html_writer::start_tag('div', $param);

        // Print meeting name label
        $param = array('class' => 'aconlabeltitle', 'id' => 'aconmeetnametitle');
        $html .= html_writer::start_tag('div', array('class' => 'aconlabeltitle', 'id' => 'aconmeetnametitle'));
        $param = array('for' => 'lblmeetingnametitle');
        $html .= html_writer::tag('label', get_string('meetingname', 'adobeconnect'), $param);
        $html .= html_writer::end_tag('div');

        // Print meeting name value
        $param = array('class' => 'aconlabeltext', 'id' => 'aconmeetnametxt');
        $html .= html_writer::start_tag('div', $param);
        $param = array('for' => 'lblmeetingname');
        $html .= html_writer::tag('label', format_string($meetingdetail->name), $param);
        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');

        // Display the meeting url and port if the user has the capabilities
        if ($meetingdetail->url) {

            $param = array('class' => 'aconmeetinforow');
            $html .= html_writer::start_tag('div', $param);

            // Print meeting URL label
            $param = array('class' => 'aconlabeltitle', 'id' => 'aconmeeturltitle');
            $html .= html_writer::start_tag('div', $param);
            $param = array('for' => 'lblmeetingurltitle');
            $html .= html_writer::tag('label', get_string('meeturl', 'adobeconnect'), $param);
            $html .= html_writer::end_tag('div');

            // Print meeting URL value
            $param = array('class' => 'aconlabeltext', 'id' => 'aconmeeturltext');
            $html .= html_writer::start_tag('div', $param);
            $param = array('for' => 'lblmeetingurl');
            $html .= html_writer::tag('label', $meetingdetail->url, $param);
            $html .= html_writer::end_tag('div');

            $html .= html_writer::end_tag('div');

        }

        if ($meetingdetail->servermeetinginfo) {
            $param = array('class' => 'aconmeetinforow');
            $html .= html_writer::start_tag('div', $param);

            // Print meeting URL label
            $param = array('class' => 'aconlabeltitle', 'id' => 'aconmeeturlinfo');
            $html .= html_writer::start_tag('div', $param);
            $param = array('for' => 'lblmeetingurlinfo');
            $html .= html_writer::tag('label', get_string('meetinfo', 'adobeconnect'), $param);
            $html .= html_writer::end_tag('div');

            // Print meeting URL value
            $param = array('class' => 'aconlabeltext', 'id' => 'aconmeeturlinfotext');
            $html .= html_writer::start_tag('div', $param);
            $param = array('target' => '_blank');
            //            $html .= html_writer::tag('label', $meetingdetail->url, $param);
            $html .= html_writer::link($meetingdetail->servermeetinginfo, get_string('meetinfotxt', 'adobeconnect'),
                $param);
            $html .= html_writer::end_tag('div');

            $html .= html_writer::end_tag('div');

        }

        // Print meeting start time label and value
        $param = array('class' => 'aconmeetinforow');
        $html .= html_writer::start_tag('div', $param);

        // Print meeting start time label
        $param = array('class' => 'aconlabeltitle', 'id' => 'aconmeetstarttitle');
        $html .= html_writer::start_tag('div', $param);
        $param = array('for' => 'lblmeetingurl');
        $html .= html_writer::tag('label', get_string('meetingstart', 'adobeconnect'), $param);
        $html .= html_writer::end_tag('div');

        // Print meeting start time value
        $param = array('class' => 'aconlabeltext', 'id' => 'aconmeetstarttxt');
        $html .= html_writer::start_tag('div', $param);
        $param = array('for' => 'lblmeetingstart');
        $html .= html_writer::tag('label', $meetingdetail->starttime, $param);
        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');

        // Print the meeting end time label and value
        $param = array('class' => 'aconmeetinforow');
        $html .= html_writer::start_tag('div', $param);

        // Print meeting end time label
        $param = array('class' => 'aconlabeltitle', 'id' => 'aconmeetendtitle');
        $html .= html_writer::start_tag('div', $param);
        $param = array('for' => 'lblmeetingendtitle');
        $html .= html_writer::tag('label', get_string('meetingend', 'adobeconnect'), $param);
        $html .= html_writer::end_tag('div');

        // Print meeting end time value
        $param = array('class' => 'aconlabeltext', 'id' => 'aconmeetendtxt');
        $html .= html_writer::start_tag('div', $param);
        $param = array('for' => 'lblmeetingend');
        $html .= html_writer::tag('label', $meetingdetail->endtime, $param);
        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');

        // Print meeting summary label and value
        $param = array('class' => 'aconmeetinforow');
        $html .= html_writer::start_tag('div', $param);

        // Print meeting summary label
        $param = array('class' => 'aconlabeltitle', 'id' => 'aconmeetsummarytitle');
        $html .= html_writer::start_tag('div', $param);
        $param = array('for' => 'lblmeetingsummarytitle');
        $html .= html_writer::tag('label', get_string('meetingintro', 'adobeconnect'), $param);
        $html .= html_writer::end_tag('div');

        // Print meeting summary value
        $param = array('class' => 'aconlabeltext', 'id' => 'aconmeetsummarytxt');
        $html .= html_writer::start_tag('div', $param);
        $param = array('for' => 'lblmeetingsummary');
        $html .= html_writer::tag('label', format_module_intro('adobeconnect', $meetingdetail, $cmid), $param);
        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');

        // Print hidden elements
        $param = array('type' => 'hidden', 'name' => 'id', 'value' => $cmid);
        $html .= html_writer::empty_tag('input', $param);
        $param = array('type' => 'hidden', 'name' => 'group', 'value' => $groupid);
        $html .= html_writer::empty_tag('input', $param);
        $param = array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey());
        $html .= html_writer::empty_tag('input', $param);

        // Print buttons
        $param = array('class' => 'aconbtnrow');
        $html .= html_writer::start_tag('div', $param);

        $param = array('class' => 'aconbtnjoin');
        $html .= html_writer::start_tag('div', $param);

        $param = array('id' => $cmid, 'sesskey' => sesskey(), 'groupid' => $groupid);
        $target = new moodle_url('/mod/adobeconnect/join.php', $param);

        $param = array(
            'type'    => 'button',
            'value'   => get_string('joinmeeting', 'adobeconnect'),
            'class'   => 'btn btn-primary btn-success mx-2',
            'name'    => 'btnname',
            'onclick' => 'window.open(\''.$target->out(false).'\', \'btnname\',
                                                 \'menubar=0,location=0,scrollbars=0,resizable=0,width=900,height=900\', 0);',
        );

        $html .= html_writer::empty_tag('input', $param);
        $html .= html_writer::end_tag('div');

        $param = array('class' => 'aconbtnroles');
        $html .= html_writer::start_tag('div', $param);
        $param = array(
            'type'  => 'submit',
            'value' => get_string('selectparticipants', 'adobeconnect'),
            'class' => 'btn btn-secondary mx-2',
            'name'  => 'participants',
        );
        $html .= html_writer::empty_tag('input', $param);
        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('form');

        return $html;
    }

    function display_controllers(
        $adobeconnect,
        $cmid,
        $scoid,
        $groupmode,
        $usrprincipal,
        $show_attendance,
        $show_recordings
    ) {


        if ($adobeconnect->last_sync_record > 0) {
            $last_rec = userdate($adobeconnect->last_sync_record, get_string('strftimedatetimeshort'));
        }
        if ($adobeconnect->last_sync_attendance > 0) {
            $last_att = userdate($adobeconnect->last_sync_attendance, get_string('strftimedatetimeshort'));
        }
        $data = (object) [
            'last_sync_record'     => $last_rec ?: null,
            'last_sync_attendance' => $last_att ?: null,
            'cmid'                 => $cmid,
            'scoid'                => $scoid,
            'groupmode'            => $groupmode,
            'usrprincipal'         => $usrprincipal,
            'attendance'           => $show_attendance,
            'recordings'           => $show_recordings,
        ];

        //        $html .= html_writer::end_tag('div');
        //
        //        $html .= html_writer::end_tag('div');

        return $this->render_from_template('adobeconnect/controls', $data);

        //return $html;
        //$html .= html_writer::link($url, get_string('removemychoice','choice'));
    }

    /** This function outpus HTML markup with links to Connect meeting recordings.
     * If a valid groupid is passed it will only display recordings that
     * are a part of the group
     *
     * @param  array - 2d array of recorded meeting and meeting details
     * @param  int - course module id
     * @param  int - group id
     * @param  int - source sco id, used to filter meetings
     *
     * @return string - HTML markup, links to recorded meetings
     */
    function display_meeting_recording($recordings, $cmid, $groupid, $sourcescoid, $showoffline)
    {
        global $CFG, $USER;

        $html = '';
        $protocol = 'http://';
        $port = ''; // Include the port number only if it is a port other than 80
        $configs = get_config('mod_adobeconnect');
        if (!empty($configs->port) and (80 != $configs->port)) {
            $port = ':'.$configs->port;
        }

        if (isset($configs->https) and (!empty($CFG->https))) {
            $protocol = 'https://';
        }
        $context = context_module::instance($cmid);
        //$context = context_course::instance($adobeconnect->course);
        $canDelete = false;
        $canManage = false;
        if (has_capability('mod/adobeconnect:managerecordings', $context, $USER->id)) {
            $canManage = true;
        }
        if (has_capability('mod/adobeconnect:deleterecordings', $context, $USER->id)) {
            $canDelete = true;
        }

        $records = array();

        if (isset($recordings['err_msg']) && $recordings['is_notification'] === 1) {
            \core\notification::info($recordings['err_msg']);
        }
        foreach ($recordings['data'] as $recording) {

            if ($recording->sourcesco != $sourcescoid) {
                continue;
            }

            $url = 'joinrecording.php?mode=online&id='.$cmid.'&recording='.$recording->recordingscoid.
                '&groupid='.$groupid.'&sesskey='.$USER->sesskey;

            if ($showoffline) {
                if (!$recording->url_offline && $recording->adobe_offline) {
                    $recording->url_offline = 'joinrecording.php?mode=offline&id='.$cmid.'&recording='
                        .$recording->recordingscoid.
                        '&groupid='.$groupid.'&sesskey='.$USER->sesskey;
                }

            }

            $name = html_entity_decode($recording->name);

            $hour = true;
            if (!$this->compareDate($recording->start_date, $recording->end_date)) {

                $hour = false;
            }
            $start_dates = $this->convertDate($recording->start_date, '%H:%M', false);
            $end_dates = $this->convertDate($recording->end_date, '%H:%M', false);
            $record = [
                'name'                 => $name,
                'url'                  => $url,
                'url_offline'          => $recording->url_offline,
                'recording_scoid'      => $recording->recordingscoid,
                'recording_id'         => $recording->id,
                'use_hour'             => $hour,
                'hideoffline'          => $recording->hideoffline,
                'hideonline'           => $recording->hideonline,
                'hiderow'              => $recording->hideonline && $recording->hideoffline,
                'deleted'              => $recording->deleted,
                'sesskey'              => $USER->sesskey,
                'startdate'            => $this->convertDate($recording->start_date),
                'enddate'              => $this->convertDate($recording->end_date),
                'start_hour'           => ($start_dates),
                'end_hour'             => ($end_dates),
                'rdate'                => $this->convertDate($recording->start_date, 'strftimedaydate'),
                'formated_create_date' => $this->convertDate($recording->create_date),
                'formated_duration'    => $this->convertToHoursMins($recording->duration),
                'adobe_offline'        => $recording->adobe_offline

            ];
            $records[] = $record;
        }
        $data = (object) [
            'records'             => $records,
            'canmanagerecordings' => $canManage,
            'candeleterecordings' => $canDelete,
            'showoffline'         => $showoffline

        ];

        return $this->render_from_template('adobeconnect/recordings', $data);

    }

    function display_meeting_attendees($attendees, $cmid, $groupid, $sourcescoid)
    {
        global $CFG, $USER;

        $data = (object) [
            'userfields'   => get_custom_fields(),
            'rows'         => array_values($attendees),
            'shortenddate' => $hour,
        ];

        $csv = $this->render_from_template('adobeconnect/attendance_csv', $data);
        return $this->render_from_template('adobeconnect/attendance', $data).$csv;

    }

    function display_no_groups_message()
    {
        $html = html_writer::tag('p', get_string('usergrouprequired', 'adobeconnect'));
        return $html;
    }

    function convertToHoursMins($time, $format = '%02d:%02d:%02d')
    {
        if ($time < 1) {
            return;
        }
        $allminutes = floor($time / 60);
        $hours = floor($allminutes / 60);
        $minutes = ($allminutes % 60);
        $seconds = ($time % 60);

        return sprintf($format, $hours, $minutes, $seconds);
    }

    function compareDate($firstDate, $secondDate)
    {
        $timestamp1 = ($firstDate);
        $timestamp2 = ($secondDate);
        $date1 = date('Y-m-d', $timestamp1);
        $date2 = date('Y-m-d', $timestamp2);
        return ($date1 == $date2);

    }

    function convertDate($time, $format = 'strftimedaydatetime', $useString = true)
    {
        $timestamp = ($time);
        //return $date;
        if ($useString) {
            return userdate($timestamp, get_string($format));
        } else {
            return userdate($timestamp, $format);
        }
    }

    function diffDate($time1, $time2, $format = 'h:i:s', $useString = true)
    {
        $date1 = date('Y-m-d h:i:s', $time1);
        $date2 = date('Y-m-d h:i:s', $time2);
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);

        $interval = $datetime1->diff($datetime2);
        return $interval;

    }
}
