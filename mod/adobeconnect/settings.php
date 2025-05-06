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

defined('MOODLE_INTERNAL') || die;

global $PAGE,$CFG;

// installation process that can't done in plugin install process moved here from db\install.php file
require_once($CFG->dirroot . '/mod/adobeconnect/db/upgradelib.php');
mod_adobeconnect_upgrade_create_roles();

if ($hassiteconfig) {
    require_once($CFG->dirroot . '/mod/adobeconnect/locallib.php');
    $PAGE->requires->js_init_call('M.mod_adobeconnect.init');

    if ($ADMIN->fulltree) {
        $settings = new theme_boost_admin_settingspage_tabs('modsettingadobeconnect', get_string('configtitle', 'mod_adobeconnect'));

        $page = new admin_settingpage('mod_adobe_connect_general', get_string('general', 'adobeconnect'));

        $name = 'mod_adobeconnect/host';
        $title = get_string('host', 'adobeconnect');
        $description = get_string('host_desc', 'adobeconnect');
        $default = 'localhost/api/xml';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
        $page->add($setting);

        $name = 'mod_adobeconnect/meethost';
        $title = get_string('meetinghost', 'adobeconnect');
        $description = get_string('meethost_desc', 'adobeconnect');
        $default = 'localhost';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
        $page->add($setting);

        $name = 'mod_adobeconnect/port';
        $title = get_string('port', 'adobeconnect');
        $description = get_string('port_desc', 'adobeconnect');
        $default = '80';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT);
        $page->add($setting);

        $name = 'mod_adobeconnect/admin_login';
        $title = get_string('admin_login', 'adobeconnect');
        $description = get_string('admin_login_desc', 'adobeconnect');
        $default = 'admin';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
        $page->add($setting);

        $name = 'mod_adobeconnect/admin_password';
        $title = get_string('admin_password', 'adobeconnect');
        $description = get_string('admin_password_desc', 'adobeconnect');
        $default = 'admin';
        $setting = new admin_setting_configpasswordunmask($name, $title, $description, $default);
        $page->add($setting);


        $name = 'mod_adobeconnect/admin_httpauth';
        $title = get_string('admin_httpauth', 'adobeconnect');
        $description = get_string('admin_httpauth_desc', 'adobeconnect');
        $default = 'my-user-id';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
        $page->add($setting);

        $name = 'mod_adobeconnect/email_login';
        $title = get_string('email_login', 'adobeconnect');
        $description = get_string('email_login_desc', 'adobeconnect');
        $default = '0';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $page->add($setting);

        $name = 'mod_adobeconnect/https';
        $title = get_string('https', 'adobeconnect');
        $description = get_string('https_desc', 'adobeconnect');
        $default = '0';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $page->add($setting);

        $name = 'mod_adobeconnect/view_own_attendance';
        $title = get_string('view_own_attendance', 'adobeconnect');
        $description = get_string('view_own_attendance_desc', 'adobeconnect');
        $default = '0';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $page->add($setting);

        $url = $CFG->wwwroot . '/mod/adobeconnect/conntest.php';
        $url = htmlentities($url, ENT_COMPAT, 'UTF-8');
        $options = 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=700,height=300';
        $str = '<div class="d-flex justify-content-center w-100"><input type="button" class="btn btn-info btn-primary" onclick="window.open(\'' . $url . '\', \'\', \'' . $options . '\');" value="' .
            get_string('testconnection', 'adobeconnect') . '" /></div>';

        $page->add(new admin_setting_heading('adobeconnect_test', '', $str));
        $page->add(new admin_setting_heading('adobeconnect_intro', '','<div class="d-none hidden">Y3JlYXRlZCBieSBzYWVpZCBzaGFyYWZp</div>'));

        $settings->add($page);

        $page = new admin_settingpage('mod_adobe_connect_offline', get_string('offline', 'adobeconnect'));

        $name = 'mod_adobeconnect/offline_group';
        $title = get_string('admin_offline_group', 'adobeconnect');
        $description = get_string('admin_offline_group_desc', 'adobeconnect');
        $default = 'Offline';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
        $page->add($setting);

        $name = 'mod_adobeconnect/showoffline';
        $title = get_string('showoffline', 'adobeconnect');
        $description = get_string('showoffline_desc', 'adobeconnect');
        $default = '1';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $page->add($setting);

        $name = 'mod_adobeconnect/showoffline';
        $title = get_string('showoffline', 'adobeconnect');
        $description = get_string('showoffline_desc', 'adobeconnect');
        $default = '1';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $page->add($setting);

        $param = new stdClass();
        $param->image = $CFG->wwwroot . '/mod/adobeconnect/pix/rl_logo.png';
        $param->url = 'https://moodle.org/plugins/view.php?plugin=mod_adobeconnect';

        $title = get_string('offline_server', 'adobeconnect');
        $page->add(new admin_setting_heading('adobeconnect_offline_server', '', $title));

        $name = 'mod_adobeconnect/use_offline';
        $title = get_string('use_offline', 'adobeconnect');
        $description = get_string('use_offline_desc', 'adobeconnect');
        $default = '0';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $page->add($setting);

        $name = 'mod_adobeconnect/offline_host';
        $title = get_string('offline_host', 'adobeconnect');
        $description = get_string('offline_host_desc', 'adobeconnect');
        $default = 'http://vcoffline.pafcoerp.com';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
        $page->add($setting);

        $name = 'mod_adobeconnect/offline_host_secret';
        $title = get_string('offline_host_secret', 'adobeconnect');
        $description = get_string('offline_host_secret_desc', 'adobeconnect');
        $default = 'secret';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
        $page->add($setting);

        $settings->add($page);

        $page = new admin_settingpage('mod_adobe_connect_exra', get_string('extra', 'adobeconnect'));

        $name = 'mod_adobeconnect/customfields';
        $title = get_string('customfields', 'adobeconnect');
        $description = get_string('customfields_desc', 'adobeconnect');
        $default = '';
        $setting = new admin_setting_configtextarea($name, $title, $description, $default);
        $page->add($setting);

        $settings->add($page);
        //$ADMIN->add('mod_adobeconnect', $settingspage);
    }

}
