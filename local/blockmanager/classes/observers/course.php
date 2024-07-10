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

        return;

        // Get the course ID from the event data
        $courseid = $event->courseid;

        if (!$course = $DB->get_record('course', array('id' => $courseid))) {
            return;
        }
        $courseformat = course_get_format($course)->get_format();

        // Add cocoon_course_overview block to the course
        $block = new \stdClass();
        $block->blockname = 'cocoon_course_intro';
        $block->parentcontextid = $DB->get_field('context', 'id', array('contextlevel' => CONTEXT_COURSE, 'instanceid' => $courseid));
        $block->pagetypepattern = 'course-view-*';
        $block->defaultregion = 'above-content';
        $block->showinsubcontexts = 0;
        $block->defaultweight = 0;
        $block->configdata = 'Tzo4OiJzdGRDbGFzcyI6MTE6e3M6NzoidGVhY2hlciI7czoxNzoi2YbYp9mFINin2LPYqtin2K8iO3M6NToiaW1hZ2UiO2k6NDIwODkxNzM3O3M6NjoiYWNjZW50IjtzOjExOiJCZXN0IFNlbGxlciI7czo1OiJ2aWRlbyI7czozNToiLy93d3cueW91dHViZS5jb20vZW1iZWQvNTdMUUk4REt3ZWMiO3M6NToic3R5bGUiO3M6MToiMCI7czo0OiJ1c2VyIjtzOjE6IjIiO3M6MTI6InNob3dfdGVhY2hlciI7czoxOiIxIjtzOjk6InZpZGVvX3VybCI7czoyODoiaHR0cHM6Ly95b3V0dS5iZS9VZER3S0k0RGNHdyI7czoxNDoiY2NuX21hcmdpbl90b3AiO3M6MToiMCI7czoxNzoiY2NuX21hcmdpbl9ib3R0b20iO3M6MToiMCI7czoxMzoiY2NuX2Nzc19jbGFzcyI7czowOiIiO30=';
        $block->timemodified = $block->timecreated = time();

        $DB->insert_record('block_instances', $block);

        // Add cocoon_course_details (enrolment) block to the course
        $block = new \stdClass();
        $block->blockname = 'cocoon_course_details';
        $block->parentcontextid = $DB->get_field('context', 'id', array('contextlevel' => CONTEXT_COURSE, 'instanceid' => $courseid));
        $block->pagetypepattern = 'course-view-*';
        $block->defaultregion = 'side-pre';
        $block->showinsubcontexts = 0;
        $block->defaultweight = 0;
        $block->configdata = 'Tzo4OiJzdGRDbGFzcyI6MTY6e3M6NToiaXRlbXMiO3M6MToiNiI7czoxNDoiY2NuX21hcmdpbl90b3AiO3M6MToiMCI7czoxNzoiY2NuX21hcmdpbl9ib3R0b20iO3M6MToiMCI7czoxMzoiY2NuX2Nzc19jbGFzcyI7czowOiIiO3M6MTE6Iml0ZW1fdGl0bGUxIjtzOjExOiIxMSDYrNmE2LPZhyI7czoxMDoiaXRlbV9pY29uMSI7czoyMjoiZmxhdGljb24tcGxheS1idXR0b24tMSI7czoxMToiaXRlbV90aXRsZTIiO3M6MjY6ItmB2KfbjNmEINmH2KfbjCDYttmF24zZhdmHIjtzOjEwOiJpdGVtX2ljb24yIjtzOjE3OiJmbGF0aWNvbi1kb3dubG9hZCI7czoxMToiaXRlbV90aXRsZTMiO3M6Mzg6Itiv2LPYqtix2LPbjCDYr9in2KbZhduMINio2Ycg2K/ZiNix2YcgIjtzOjEwOiJpdGVtX2ljb24zIjtzOjE0OiJmbGF0aWNvbi1rZXktMSI7czoxMToiaXRlbV90aXRsZTQiO3M6NjE6ItmC2KfYqNmE24zYqiDYr9iz2KrYsdiz24wg2K/YsSDYr9iz2Krar9in2Ycg2YfYp9uMINmF2K7YqtmE2YEiO3M6MTA6Iml0ZW1faWNvbjQiO3M6MTk6ImZsYXRpY29uLXJlc3BvbnNpdmUiO3M6MTE6Iml0ZW1fdGl0bGU1IjtzOjI3OiLZh9mF2LHYp9mHINio2Kcg2KrZhdix24zZhiAiO3M6MTA6Iml0ZW1faWNvbjUiO3M6MTQ6ImZsYXRpY29uLWZsYXNoIjtzOjExOiJpdGVtX3RpdGxlNiI7czo0ODoi2YXYr9ix2qkg2YXYudiq2KjYsSDYqNinINmC2KfYqNmE24zYqiDYqtix2KzZhdmHIjtzOjEwOiJpdGVtX2ljb242IjtzOjE0OiJmbGF0aWNvbi1tZWRhbCI7fQ==';
        $block->timemodified = $block->timecreated = time();

        $DB->insert_record('block_instances', $block);

        // Add cocoon_course_overview block to the course
        $block = new \stdClass();
        $block->blockname = 'cocoon_course_overview';
        $block->parentcontextid = $DB->get_field('context', 'id', array('contextlevel' => CONTEXT_COURSE, 'instanceid' => $courseid));
        $block->pagetypepattern = 'course-view-*';
        $block->defaultregion = 'above-content';
        $block->showinsubcontexts = 0;
        $block->defaultweight = 1;
        $block->configdata = '';
        $block->timemodified = $block->timecreated = time();

        $DB->insert_record('block_instances', $block);

        // Add cocoon_course_instructor block to the course
        $block = new \stdClass();
        $block->blockname = 'cocoon_course_instructor';
        $block->parentcontextid = $DB->get_field('context', 'id', array('contextlevel' => CONTEXT_COURSE, 'instanceid' => $courseid));
        $block->pagetypepattern = 'course-view-*';
        $block->defaultregion = 'below-content';
        $block->showinsubcontexts = 0;
        $block->defaultweight = 1;
        $block->configdata = '';
        $block->timemodified = $block->timecreated = time();

        $DB->insert_record('block_instances', $block);

        // Add tags block to the course
        $block = new \stdClass();
        $block->blockname = 'tags';
        $block->parentcontextid = $DB->get_field('context', 'id', array('contextlevel' => CONTEXT_COURSE, 'instanceid' => $courseid));
        $block->pagetypepattern = 'course-view-*';
        $block->defaultregion = 'side-pre';
        $block->showinsubcontexts = 0;
        $block->defaultweight = 1;
        $block->configdata = '';
        $block->timemodified = $block->timecreated = time();

        $DB->insert_record('block_instances', $block);

        // Add cocoon_more_courses block to the course
        $block = new \stdClass();
        $block->blockname = 'cocoon_more_courses';
        $block->parentcontextid = $DB->get_field('context', 'id', array('contextlevel' => CONTEXT_COURSE, 'instanceid' => $courseid));
        $block->pagetypepattern = 'course-view-*';
        $block->defaultregion = 'below-content';
        $block->showinsubcontexts = 0;
        $block->defaultweight = 1;
        $block->configdata = 'Tzo4OiJzdGRDbGFzcyI6MTI6e3M6NToidGl0bGUiO3M6MjY6Itiv2YjYsdmHINmH2KfbjCDZhdix2KrYqNi3IjtzOjEwOiJob3Zlcl90ZXh0IjtzOjIxOiLZhdi02KfZh9iv2Ycg2K/ZiNix2YciO3M6MTI6ImhvdmVyX2FjY2VudCI7czoyMToi2b7YsdmB2LHZiNi0INiq2LHbjNmGIjtzOjEyOiJjb3Vyc2VfaW1hZ2UiO3M6MToiMSI7czoxMToiZGVzY3JpcHRpb24iO3M6MToiMCI7czo1OiJwcmljZSI7czoxOiIxIjtzOjk6ImVucm9sX2J0biI7czoxOiIwIjtzOjE0OiJlbnJvbF9idG5fdGV4dCI7czoyMjoi2LTYsdqp2Kog2K/YsSDYr9mI2LHZhyI7czo3OiJjb3Vyc2VzIjthOjM6e2k6MDtzOjE6IjUiO2k6MTtzOjE6IjMiO2k6MjtzOjE6IjIiO31zOjE0OiJjY25fbWFyZ2luX3RvcCI7czoxOiIwIjtzOjE3OiJjY25fbWFyZ2luX2JvdHRvbSI7czoxOiIwIjtzOjEzOiJjY25fY3NzX2NsYXNzIjtzOjA6IiI7fQ==';
        $block->timemodified = $block->timecreated = time();

        $DB->insert_record('block_instances', $block);

    }
}
