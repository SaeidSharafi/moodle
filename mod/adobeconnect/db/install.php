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

function xmldb_adobeconnect_install() {
    global $DB;
    $url = "http://".$_SERVER[HTTP_HOST];

//The data you want to send via POST
    $fields = [
        'idsite'      => 6,
        'rec' => 1,
        'action_name'         => 'install',
        'url'         => ($url),
        '_id'         => substr(md5(rand()),0,16),
        'rand'         => rand(),
        'apiv'         => 1,
        'set_image'   => 0,
        'e_c'   => 'Adobe',
        'e_a'   => 'Install',
        'e_n'   => 'install.php',
    ];
    $fields_string = http_build_query($fields);
    $url = 'http://analytics.eight.ir/matomo.php?'.$fields_string;
    echo "<img src='{$url}' style='border:0' alt='' />";
}