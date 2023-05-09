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
 * Version details
 *
 * @package    format_remuiformat
 * @copyright  2021
 *  Wisdmlabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_blockmanager\observers;

use core\event\course_created;

class course {

    /**
     * Observer for \core\event\course_created event.
     *
     * @param course_created $event
     * @return void
     */
    public static function created(course_created $event) :void{
        global $DB;

        // Get the course ID from the event data
        $courseid = $event->courseid;

        // Add a new block to the course
        $block = new \stdClass();
        $block->blockname = 'cocoon_course_intro';
        $block->parentcontextid = $DB->get_field('context', 'id', array('contextlevel' => CONTEXT_COURSE, 'instanceid' => $courseid));
        $block->pagetypepattern = 'course-view-*';
        $block->defaultregion = 'above-content';
        $block->showinsubcontexts = 0;
        $block->defaultweight = 0;
        $block->configdata = 'Tzo4OiJzdGRDbGFzcyI6NTp7czo3OiJ0ZWFjaGVyIjtzOjk6IkFsaSBUdWZhbiI7czo1OiJpbWFnZSI7aTo0MjA4OTE3Mzc7czo2OiJhY2NlbnQiO3M6MTE6IkJlc3QgU2VsbGVyIjtzOjU6InZpZGVvIjtzOjM1OiIvL3d3dy55b3V0dWJlLmNvbS9lbWJlZC81N0xRSThES3dlYyI7czo1OiJzdHlsZSI7czoxOiIwIjt9';
        $block->timemodified = $block->timecreated = time();

        $DB->insert_record('block_instances', $block);

    }
}
