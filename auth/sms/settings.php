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
 * auth_sms message processor settings
 *
 * @package    auth_sms
 * @copyright  2022 Morteza Ahmadi <m.ahmadi.ma@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/locallib.php');

if(!class_exists('admin_setting_begin_section')) {
	class admin_setting_begin_section extends admin_setting_heading {
		public function output_html($data, $query='') {
			return '<div style="margin: 1rem; border: 1px solid black;">' . '<h3 style="padding: 1rem; background-color: rgba(0, 0, 0, 0.1);">' . $this->visiblename . '</h3><div style="padding: 1rem;">';
		}
	}
}

if(!class_exists('admin_setting_end_section')) {
	class admin_setting_end_section extends admin_setting_heading {

		public function __construct($name) {
			parent::__construct($name, '', '', '');
		}

		public function output_html($data, $query='') {
			return '</div></div>';
		}
	}
}

if ($ADMIN->fulltree){

	$settings->add(new admin_setting_configselect('auth_sms/smstype', get_string('smstype', 'auth_sms'), 
			'',
			'sms.ir',
			[
				'sms.ir' => 'sms.ir',
				'magfa.com' => 'magfa.com',
			]
		));

	$settings->add(new admin_setting_configselect('auth_sms/smsphoneplace', get_string('smsphoneplace', 'auth_sms'), 
		get_string('smsphoneplace_desc', 'auth_sms'), 'phone2', auth_sms_get_user_info_field_name()));

	$a = new stdClass();
	$a->href = $CFG->wwwroot . '/admin/settings.php?section=manageauths';
	$a->url = $CFG->wwwroot . '/login/signup.php?forgotten_password=1';
	$settings->add(new admin_setting_configcheckbox('auth_sms/smsforgottenpassword', get_string('smsforgottenpassword', 'auth_sms'),
		get_string('smsforgottenpassword_desc', 'auth_sms', $a), 0));

	//sms.ir
	if(true) {
		$settings->add(new admin_setting_begin_section('auth_sms/smsir_begin_section',
			new lang_string('settings', 'core_plugin') . ' (sms.ir)', 
			''));

		$settings->add(new admin_setting_configtext('auth_sms/smsapikey', get_string('smsapikey', 'auth_sms'), 
			'https://sms.ir (Api key)', '', PARAM_RAW));
		
		//method1
		$settings->add(new admin_setting_heading('auth_sms/method1',
			new lang_string('method', 'auth_sms') . ' 1',
			new lang_string('method1_desc', 'auth_sms')));
		
			
		$settings->add(new admin_setting_configtext('auth_sms/smssecretkey', get_string('smssecretkey', 'auth_sms'), 
			'https://sms.ir (Secret key)', '', PARAM_RAW));

		//method2
		$settings->add(new admin_setting_heading('auth_sms/method2',
			new lang_string('method', 'auth_sms') . ' 2',
			new lang_string('method2_desc', 'auth_sms')));

		$settings->add(new admin_setting_configtext('auth_sms/smstemplateid', get_string('smstemplateid', 'auth_sms'), 
			get_string('smstemplateid_desc', 'auth_sms'), '', PARAM_RAW));

		$settings->add(new admin_setting_end_section('auth_sms/smsir_end_section'));
	}

	//magfa.com
	if(true) {
		$settings->add(new admin_setting_begin_section('auth_sms/magfa_begin_section',
			new lang_string('settings', 'core_plugin') . ' (magfa.com)', 
			''));

		$settings->add(new admin_setting_configtext('auth_sms/smsmagfausername', get_string('username', 'core'), 
			'', '', PARAM_RAW));

		$settings->add(new admin_setting_configpasswordunmask('auth_sms/smsmagfapassword', get_string('password', 'core'), 
			get_string('smsmagfapassword_desc', 'auth_sms'), '', PARAM_RAW));

		$settings->add(new admin_setting_configtext('auth_sms/smsmagfadomain', get_string('smsmagfadomain', 'auth_sms'), 
			get_string('smsmagfadomain_desc', 'auth_sms'), '', PARAM_RAW));

		$settings->add(new admin_setting_configtext('auth_sms/smsmagfalinenumber', get_string('smsmagfalinenumber', 'auth_sms'), 
			get_string('smsmagfalinenumber_desc', 'auth_sms'), '', PARAM_RAW));
		
		$settings->add(new admin_setting_configtextarea('auth_sms/smsmagfatemplatetext', get_string('smsmagfatemplatetext', 'auth_sms'), 
			get_string('smsmagfatemplatetext_desc', 'auth_sms'), '', PARAM_RAW));
		
		$settings->add(new admin_setting_end_section('auth_sms/magfa_end_section'));
	}
	

}