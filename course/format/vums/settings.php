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
 * Cards Format - A topics based format that uses card layout to diaply the content.
 *
 * @package    format_vums
 * @copyright  2019 Wisdmlabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;

if ($ADMIN->fulltree) {
    // Default length of sumary of the section/activities.
    $name = 'format_vums/defaultsectionsummarymaxlength';
    $title = get_string('defaultsectionsummarymaxlength', 'format_vums');
    $description = get_string('defaultsectionsummarymaxlength_desc', 'format_vums');
    $default = 100;
    $settings->add(new admin_setting_configtext($name, $title, $description, $default, PARAM_INT));

    // Default setting hide general section when empty.
    $name = 'format_vums/hidegeneralsectionwhenempty';
    $title = new lang_string('hidegeneralsectionwhenempty', 'format_vums');
    $description = new lang_string('hidegeneralsectionwhenempty_help', 'format_vums');
    $default = 0;
    $settings->add(new admin_setting_configselect(
        $name,
        $title,
        $description,
        $default,
        array(
            0 => new lang_string('show'),
            1 => new lang_string('hide')
        )
    ));

    // Default setting hide general section when empty.
    $name = 'format_vums/vumscourseformat';
    $title = new lang_string('vumscourseformat', 'format_vums');
    $description = new lang_string('vumscourseformat_help', 'format_vums');
    $default = 1;
    $settings->add(new admin_setting_configselect(
        $name,
        $title,
        $description,
        $default,
        array(
            1 => new lang_string('vumscourseformat_list', 'format_vums')
        )
    ));
    $name = 'format_vums/tilebgcolor';
    $title = get_string('tilebgcolor', 'format_vums');
    $description = get_string('tilebgcolor_desc', 'format_vums');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#0a9bb2');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    global $DB;
    $activitytypes = array();

    // Query to get all activity modules.
    $modules = $DB->get_records('modules', array('visible' => 1));

    foreach ($modules as $module) {
        $activitytypes[$module->name] = get_string('modulename', $module->name);
    }

    // Create the multi-select element.
    $settings->add(new admin_setting_configmultiselect(
        'format_vums/section1activitytypes',
        get_string('section1activitytypes', 'format_vums'),
        get_string('section1activitytypes_desc', 'format_vums'),
        array(),
        $activitytypes
    ));

    $settings->add(new admin_setting_configmultiselect(
        'format_vums/section2activitytypes',
        get_string('section2activitytypes', 'format_vums'),
        get_string('section2activitytypes_desc', 'format_vums'),
        array(),
        $activitytypes
    ));

    $settings->add(new admin_setting_configmultiselect(
        'format_vums/section3activitytypes',
        get_string('section3activitytypes', 'format_vums'),
        get_string('section3activitytypes_desc', 'format_vums'),
        array(),
        $activitytypes
    ));

    $settings->add(new admin_setting_configmultiselect(
        'format_vums/section4activitytypes',
        get_string('section4activitytypes', 'format_vums'),
        get_string('section4activitytypes_desc', 'format_vums'),
        array(),
        $activitytypes
    ));

    $settings->add(new admin_setting_configmultiselect(
        'format_vums/section5activitytypes',
        get_string('section5activitytypes', 'format_vums'),
        get_string('section5activitytypes_desc', 'format_vums'),
        array(),
        $activitytypes
    ));

    $settings->add(new admin_setting_configmultiselect(
        'format_vums/section6activitytypes',
        get_string('section6activitytypes', 'format_vums'),
        get_string('section6activitytypes_desc', 'format_vums'),
        array(),
        $activitytypes
    ));

    $settings->add(new admin_setting_configmultiselect(
        'format_vums/section7activitytypes',
        get_string('section7activitytypes', 'format_vums'),
        get_string('section7activitytypes_desc', 'format_vums'),
        array(),
        $activitytypes
    ));

}
