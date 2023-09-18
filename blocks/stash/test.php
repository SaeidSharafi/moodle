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
 * Trade edit page.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

// echo 'hi';

$user = core_user::get_user(3);

$context = context_course::instance(2);
// print_object($context);

$contextlist = new \core_privacy\local\request\approved_contextlist($user, 'block_stash', [$context->id]);
// print_object($contextlist);

\block_stash\privacy\provider::_delete_data_for_user($contextlist);
$contextid = required_param('contextid', PARAM_INT);

\block_stash\external\dropwidget_select_data::get_all_drop_data($contextid);
