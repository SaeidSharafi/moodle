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
 * Contact site support.
 *
 * @copyright 2022 Simey Lameze <simey@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */
require_once('../config.php');
require_once('../theme/pafco/classes/settings.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/page/faq.php');
$PAGE->set_title(get_string('faq', 'theme_pafco'));
$PAGE->set_heading(get_string('faq', 'theme_pafco'));
if (isloggedin()){
    $PAGE->set_pagelayout('my');
}else{
    $PAGE->set_pagelayout('basepage');
}

$renderer = $PAGE->get_renderer('core');
$themesettings = new theme_pafco_settings();
$templatecontext = $themesettings->faq();


echo $OUTPUT->header();

echo $OUTPUT->render_from_template('theme_pafco/faq', $templatecontext);

echo $OUTPUT->footer();
