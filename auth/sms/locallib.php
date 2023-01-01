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
 * locallib.php
 *
 * @package   auth_sms
 * @copyright 2022 Morteza Ahmadi <m.ahmadi.ma@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function auth_sms_random_number($length=5) {
    $result = '';
    for($i = 0; $i < $length; $i++) {
        $result .= rand(0, 9);
    }
    return $result;
}

function auth_sms_get_user_info_field() {
    global $DB;
    $fields = $DB->get_records('user_info_field');
    return $fields;
}

function auth_sms_get_user_info_field_name() {
    $fields = auth_sms_get_user_info_field();
    $out['phone2'] = get_string('phone2', 'core');
    foreach($fields as $k => $v) {
        $out[$k] = $v->name;
    }
    return $out;
}

function auth_sms_get_phone_place() {
    $config = get_config('auth_sms');
    if(!isset($config->smsphoneplace) || empty($config->smsphoneplace)) {
        return 'phone2';
    }
    return $config->smsphoneplace;
}

function auth_sms_get_template_text($param) {
    $config = get_config('auth_sms');
    if(!isset($config->smsmagfatemplatetext) || empty($config->smsmagfatemplatetext)) {
        return $param;
    }
    return preg_replace('/\{code\}/', $param, $config->smsmagfatemplatetext);
}

function auth_sms_clear_session() {
    global $SESSION;
    $SESSION->activation_code = false;
    $SESSION->phone2 = false;
    $SESSION->forgotten_password = false;
}