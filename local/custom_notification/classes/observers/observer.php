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
 * Definition of core event observers.
 *
 * The observers defined in this file are notified when respective events are triggered. All plugins
 * support this.
 *
 * For more information, take a look to the documentation available:
 *     - Events API: {@link http://docs.moodle.org/dev/Event_2}
 *     - Upgrade API: {@link http://docs.moodle.org/dev/Upgrade_API}
 *
 * @package   core
 * @category  event
 */
namespace local_custom_notification\observers;

defined('MOODLE_INTERNAL') || die();

class observer {
    public static function activity_created(\core\event\course_module_created $event) {
        global $DB;

        // Get course ID and context


        $courseid = $event->courseid;
        $context = \context_course::instance($courseid);

        // Get all students in the course
        $students = get_enrolled_users($context, 'mod/assign:submit'); // Adjust capability as needed

        // Prepare the notification message
        $activityurl = (new \moodle_url('/mod/' . $event->other['modulename'] . '/view.php', ['id' => $event->contextinstanceid]))->out(false);
        $activityname = $event->other['name']; // Name of the activity

        // Send message to all students
        foreach ($students as $student) {
            $task = new \local_custom_notification\task\send_notification();
            // Set custom data for the task
            $customdata = new \stdClass();
            $customdata->studentid = $student->id;
            $customdata->contextinstanceid = $event->contextinstanceid;
            $customdata->activityname =$activityname;
            $customdata->activityurl = $activityurl;
            $customdata->activitycreatedat = $event->timecreated;

            $task->set_custom_data($customdata);

            // Queue the task for background processing
            \core\task\manager::queue_adhoc_task($task);
        }
    }
}
