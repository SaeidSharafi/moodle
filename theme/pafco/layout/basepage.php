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
 * Frontpage layout for the moove theme.
 *
 * @package    theme_moove
 * @copyright  2022 Willian Mano {@link https://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

defined('MOODLE_INTERNAL') || die();
include($CFG->dirroot . '/theme/pafco/ccn/ccn_themehandler.php');
array_push($extraclasses, "ccn_context_frontend");
$bodyclasses = implode(" ",$extraclasses);
$bodyattributes = $OUTPUT->body_attributes($bodyclasses);
include($CFG->dirroot . '/theme/pafco/ccn/ccn_themehandler_context.php');

if (isloggedin()) {
    echo $OUTPUT->render_from_template('theme_pafco/ccn_mdl_400/ccn_my', $templatecontext);
} else {
    $templatecontext = array_merge($templatecontext,frontpage($PAGE->theme));
    echo $OUTPUT->render_from_template('theme_pafco/basepage', $templatecontext);

}
