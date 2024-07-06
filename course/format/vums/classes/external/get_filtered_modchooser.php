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
 * Provides format_vums\external\move_activities trait.
 *
 * @package     format_vums
 * @category    external
 * @copyright   2018 Wisdmlabs
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_vums\external;
defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_value;

require_once($CFG->libdir.'/externallib.php');

/**
 * Trait implementing the external function format_vums_move_activities
 */
trait get_filtered_modchooser {

    public static function get_filtered_modchooser_parameters() {
        return new external_function_parameters(
            array(
                'sectionid' => new external_value(PARAM_INT, 'Section ID'),
                'courseid' => new external_value(PARAM_INT, 'Course ID'),
            )
        );
    }

    public static function get_filtered_modchooser($sectionid,$courseid) {
        global $DB, $USER;

        $coursecontext = \context_course::instance($courseid);
        self::validate_context($coursecontext);
        $course = get_course($courseid);
        $contentitemservice = \core_course\local\factory\content_item_service_factory::get_content_item_service();

        $contentitems = $contentitemservice->get_content_items_for_user_in_course($USER, $course,['secid' => $sectionid]);

        return ['content_items' => $contentitems];
    }

    public static function get_filtered_modchooser_returns() {
        return new \external_single_structure([
            'content_items' => new \external_multiple_structure(
                \core_course\local\exporters\course_content_item_exporter::get_read_structure()
            ),
        ]);
    }
}
