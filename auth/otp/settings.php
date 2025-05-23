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
 * Admin settings and defaults
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_heading('auth_otp/security',
        new lang_string('security', 'admin'), ''));

    $settings->add(new admin_setting_configcheckbox('auth_otp/enablemagfa',
        get_string('enablemagfa', 'auth_otp'),
        get_string('enablemagfa_help', 'auth_otp'), 0, PARAM_INT));

    $settings->add(new admin_setting_configtext('auth_otp/magfa_username',
        get_string('magfa_username', 'auth_otp'),
        get_string('magfa_username_help', 'auth_otp'), '', PARAM_TEXT));

    $settings->add(new admin_setting_configtext('auth_otp/magfa_password',
        get_string('magfa_password', 'auth_otp'),
        get_string('magfa_password_help', 'auth_otp'), '', PARAM_TEXT));

    $settings->add(new admin_setting_configtext('auth_otp/magfa_number',
        get_string('magfa_number', 'auth_otp'),
        get_string('magfa_number_help', 'auth_otp'), '', PARAM_TEXT));

    $settings->add(new admin_setting_configtext('auth_otp/magfa_domain',
        get_string('magfa_domain', 'auth_otp'),
        get_string('magfa_domain_help', 'auth_otp'), 'magfa', PARAM_TEXT));

    $settings->add(new admin_setting_configtextarea('auth_otp/magfa_templatetext',
        get_string('magfa_templatetext', 'auth_otp'),
        get_string('magfa_templatetext_help', 'auth_otp'), get_string('magfa_templatetext_default', 'auth_otp'), PARAM_TEXT));

    //$settings->add(new class(
    //    'auth_otp/minrequestperiod',
    //    get_string('minrequestperiod', 'auth_otp'),
    //    get_string('minrequestperiod_help', 'auth_otp')
    //) extends admin_setting_configtext {
    //    public function __construct($name, $visiblename, $description) {
    //        $readers = get_log_manager()->get_readers('\core\log\sql_reader');
    //        $logreader = reset($readers);
    //        parent::__construct($name, $visiblename, $description, $logreader ? 300 : 0, PARAM_INT);
    //        if (!$logreader && !empty($this->get_setting())) {
    //            $this->description .= ' ' . get_string('logstorerequired', 'auth_otp',
    //                    (string)new moodle_url('/admin/settings.php', ['section' => 'managelogging'])
    //                );
    //        }
    //    }
    //});

    $authplugin = get_auth_plugin('otp');
    display_auth_lock_options($settings, $authplugin->authtype,
        $authplugin->userfields, get_string('auth_fieldlocks_help', 'auth'), false, false);

}
