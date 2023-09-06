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
defined('MOODLE_INTERNAL') || die();

function xmldb_adobeconnect_upgrade($oldversion = 0)
{

    global $CFG, $DB;

    $dbman = $DB->get_manager();

//===== 1.9.0 upgrade line ======//
    if ($oldversion < 2010120800) {

        /// Define field introformat to be added to survey
        $table = new xmldb_table('adobeconnect');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

        /// Conditionally launch add field introformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // conditionally migrate to html format in intro
        if ($CFG->texteditors !== 'textarea') {
            $rs = $DB->get_recordset('adobeconnect', array('introformat' => FORMAT_MOODLE), '', 'id,intro,introformat');
            foreach ($rs as $s) {
                $s->intro = text_to_html($s->intro, false, false, true);
                $s->introformat = FORMAT_HTML;
                $DB->update_record('adobeconnect', $s);
                upgrade_set_timeout();
            }
            $rs->close();
        }

        /// adobeconnect savepoint reached
        upgrade_mod_savepoint(true, 2010120800, 'adobeconnect');
    }

    if ($oldversion < 2011041400) {

        // Changing precision of field meeturl on table adobeconnect to (60)
        $table = new xmldb_table('adobeconnect');
        $field = new xmldb_field('meeturl', XMLDB_TYPE_CHAR, '60', null, null, null, null, 'templatescoid');

        // Launch change of precision for field meeturl
        $dbman->change_field_precision($table, $field);

        // adobeconnect savepoint reached
        upgrade_mod_savepoint(true, 2011041400, 'adobeconnect');
    }

    if ($oldversion < 2012012250) {
        $table = new xmldb_table('adobeconnect');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', true, true, null, 0, 'introformat');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // adobeconnect savepoint reached
        upgrade_mod_savepoint(true, 2012012500, 'adobeconnect');

    }
    if ($oldversion < 2020090301) {

        $key1 = new xmldb_key('primary');
        $key1->set_attributes(XMLDB_KEY_PRIMARY, array('id'), null, null);

        $index1 = new xmldb_index('instanceid_idx', XMLDB_INDEX_NOTUNIQUE, ['instanceid']);
        $index2 = new xmldb_index('recordingscoid_idx', XMLDB_INDEX_NOTUNIQUE, ['recordingscoid']);

        $table = new xmldb_table('adobeconnect_recordings');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'id');
        $table->add_field('recordingscoid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'instanceid');
        $table->add_field('groupid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'recordingscoid');

        $table->addKey($key1);

        $table->addIndex($index1);
        $table->addIndex($index2);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        // adobeconnect savepoint reached
        upgrade_mod_savepoint(true, 2020090301, 'adobeconnect');

    }
    if ($oldversion < 2021031200) {

        // Define field id to be added to adobeconnect.
        $table = new xmldb_table('adobeconnect');
        $field = new xmldb_field('showoffline', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'timemodified');
        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field,true,true);
        }

        // Adobeconnect savepoint reached.
        upgrade_mod_savepoint(true, 2021031200, 'adobeconnect');
    }

    if ($oldversion < 2021060800) {

        $table = new xmldb_table('adobeconnect');
        $field = new xmldb_field('last_sync_record', XMLDB_TYPE_INTEGER, '10', null, null, null, 0, 'timemodified');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('last_sync_attendance', XMLDB_TYPE_INTEGER, '10', null, null, null, 0, 'last_sync_attendance');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }


        $table = new xmldb_table('adobeconnect_recordings');
        $table->deleteField("groupid");

        $field = new xmldb_field('sourcesco', XMLDB_TYPE_INTEGER, '10', null, null, null, 0, 'recordingscoid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'recordingscoid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('url', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'name');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('start_date', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'groupid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('end_date', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'start_date');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('create_date', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'end_dates');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('modified', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'create_date');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }


        $field = new xmldb_field('duration', XMLDB_TYPE_INTEGER, '20', null, null, null, '0', 'create_date');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }


        $field = new xmldb_field('hideoffline', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'groupid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('hideonline', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'hideoffline');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('hiderow', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'hideonline');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }



        $table = new xmldb_table('adobeconnect_attendees');

        // Adding fields to table adobeconnect_attendeess.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('email', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('session_name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sco_name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('participant_name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table adobeconnect_attendeess.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table adobeconnect_attendeess.
        $table->add_index('instanceid_idx', XMLDB_INDEX_NOTUNIQUE, ['instanceid']);
        $table->add_index('email_idx', XMLDB_INDEX_NOTUNIQUE, ['email']);

        // Conditionally launch create table for adobeconnect_attendeess.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


        $table = new xmldb_table('adobeconnect_attendance');

        // Adding fields to table adobeconnect_attendeess.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('attendee_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('start_date', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('end_date', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table adobeconnect_attendeess.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table adobeconnect_attendeess.
        $table->add_index('attendee_id_idx', XMLDB_INDEX_NOTUNIQUE, ['attendee_id']);

        // Conditionally launch create table for adobeconnect_attendeess.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


        // Adobeconnect savepoint reached.
        upgrade_mod_savepoint(true, 2021060800, 'adobeconnect');
    }
    if ($oldversion < 2021081100) {

        // Define field id to be added to adobeconnect.
        $table = new xmldb_table('adobeconnect_recordings');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'hiderow');
        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Adobeconnect savepoint reached.
        upgrade_mod_savepoint(true, 2021081100, 'adobeconnect');
    }
    return true;

}
