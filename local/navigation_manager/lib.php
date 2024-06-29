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

defined('MOODLE_INTERNAL') || die();

/**
 * Extend the global navigation tree by adding your plugin nodes.
 *
 * @param  navigation_node  $navref  An object representing the navigation tree node of your plugin
 * @param  stdClass  $course
 * @param  context  $context
 */
function local_navigation_manager_extend_navigation_course(
    navigation_node $parentnode,
    stdClass $course,
    context_course $context
) {
    if (!has_capability('local/navigation_manager:view_badgesview', $context)) {

        $reportsnode = $parentnode->get('badgesview');
        if ($reportsnode) {
            $parentnode->children->remove('badgesview');
        }
        $reportsnode = $parentnode->get('coursebadges');
        if ($reportsnode) {
            $parentnode->children->remove('coursebadges');
        }
    }
    if (!has_capability('local/navigation_manager:view_competencies', $context)) {

        $reportsnode = $parentnode->get('competencies');
        if ($reportsnode) {
            $parentnode->children->remove('competencies');
        }
    }
    if (!has_capability('local/navigation_manager:view_filtermanagement', $context)) {

        $reportsnode = $parentnode->get('filtermanagement');
        if ($reportsnode) {
            $parentnode->children->remove('filtermanagement');
        }
    }
    if (!has_capability('local/navigation_manager:view_unenrolself', $context)) {

        $reportsnode = $parentnode->get('unenrolself');
        if ($reportsnode) {
            $parentnode->children->remove('unenrolself');
        }
    }
    if (!has_capability('local/navigation_manager:view_coursecompletion', $context)) {

        $reportsnode = $parentnode->get('coursecompletion');
        if ($reportsnode) {
            $parentnode->children->remove('coursecompletion');
        }
    }
    // Make sure this is a teacher viewing the course.
    if (!has_capability('local/navigation_manager:view_course_report', $context)) {
        // Find the 'Reports' node in the navigation tree.
        $reportsnode = $parentnode->get('coursereports');
        if ($reportsnode) {
            // Remove the 'Reports' node.
            $parentnode->children->remove('coursereports');
        }

    }
}

function local_navigation_manager_extend_navigation(
    global_navigation $nav
) {
    //echo implode(',', $nav->get_children_key_list())."<br>";

}

function local_navigation_manager_extend_settings_navigation(
    settings_navigation $nav,
    context $context
) {
    //echo implode(',', $nav->get_children_key_list())."<br>";
    //die();
}
