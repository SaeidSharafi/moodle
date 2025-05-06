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
 * Strings for component 'auth_sms', language 'en'.
 *
 * @package   auth_sms
 * @copyright 2022 Morteza Ahmadi <m.ahmadi.ma@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['auth_smsdescription'] = 'Users can sign in and create valid accounts with mobile sms confirm.';
$string['pluginname'] = 'SMS';
$string['activation_code'] = 'Activation code';
$string['invalid_activation_code'] = 'Invalid activation code';
$string['success_signup_title'] = 'Successful signing up';
$string['success_signup'] = 'Your signing up was successful';
$string['phone2exists'] = 'This mobile was already registerd.';
$string['smsapikey'] = 'Api Key';
$string['smssecretkey'] = 'Secret Key';
$string['method'] = 'Method';
$string['method1_desc'] = 'For old version (https://ip.sms.ir)';
$string['method2_desc'] = 'For new version (https://app.sms.ir)';
$string['smstemplateid'] = 'Template id';
$string['smstemplateid_desc'] = 'From https://app.sms.ir/fast-send/template';
$string['wrong_settings'] = 'Wrong settings, please contact with admin';
$string['smsmagfapassword_desc'] = 'Password of webservice (from magfa.com)';
$string['smsmagfadomain'] = 'Domain';
$string['smsmagfadomain_desc'] = 'It is the word "magfa" typically.';
$string['smsmagfalinenumber'] = 'Line number';
$string['smsmagfalinenumber_desc'] = '3000xxxxxxxx';
$string['smstype'] = 'Type';
$string['smsphoneplace'] = 'Place of mobile';
$string['smsphoneplace_desc'] = 'The mobile can be saved as phone2 or profile fields.';
$string['smsmagfatemplatetext'] = 'Template of message';
$string['smsmagfatemplatetext_desc'] = 'This message is sent to the user. You can use variable "{code}".';
$string['not_exist_error'] = 'It does not exist.';
$string['password_mismatch_error'] = 'Password mismatch';
$string['change_password_success'] = 'You changed password successfully.';
$string['smsforgottenpassword'] = 'Forgotten password';
$string['smsforgottenpassword_desc'] = 'After checking this parameter, Please go to <a target="_blank" href="{$a->href}">adress</a> and set "Forgotten password URL (forgottenpasswordurl)" with "{$a->url}"';