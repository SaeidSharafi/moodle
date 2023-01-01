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
 * User sign-up form.
 *
 * @package    auth_sms
 * @subpackage auth
 * @copyright  2022 Morteza Ahmadi <m.ahmadi.ma@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->dirroot . '/auth/sms/locallib.php');

class login_signup_form extends moodleform implements renderable, templatable {

    function definition() {
        global $USER, $CFG, $SESSION;
        $mform = $this->_form;
        $config = get_config('auth_sms');

        if(!isset($SESSION->activation_code) || !$SESSION->activation_code) {
            $mform->addElement('text', 'phone2', get_string('phone2'), 'maxlength="100" size="12" autocapitalize="none" placeholder="09123456789"');
            $mform->setType('phone2', PARAM_ALPHANUM);
            $mform->addRule('phone2', '', 'required', null, 'client');

            if(isset($config->smsforgottenpassword) && $config->smsforgottenpassword) {
                //$mform->addElement('checkbox', 'forgotten_password', get_string('forgotten', 'core'));
                $mform->setDefault('forgotten_password', 1);
                $mform->addElement('hidden', 'forgotten_password');
                //if(isset($_GET['forgotten_password'])) {
                //    $mform->setDefault('forgotten_password', 1);
                //}
            } else {
                if(isset($_GET['forgotten_password'])) {
                    print_error('error', 'core');
                }
            }

            $mform->addElement('hidden', 'type');
            $mform->setDefault('type', 'phone2');
        } else {
            if(isset($SESSION->forgotten_password) && $SESSION->forgotten_password) {

                $mform->addElement('header', 'activationcodeheader', get_string('activation_code', 'auth_sms'), '');

                $mform->addElement('text', 'activation_code', get_string('activation_code', 'auth_sms'), 'maxlength="100" size="12" autocapitalize="none"');
                $mform->setType('activation_code', PARAM_RAW);
                $mform->addRule('activation_code', '', 'required', null, 'client');

                if (!empty($CFG->passwordpolicy)){
                    $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
                }
                $mform->addElement('password', 'password', get_string('password'), 'maxlength="32" size="12"');
                $mform->setType('password', core_user::get_property_type('password'));
                $mform->addRule('password', get_string('missingpassword'), 'required', null, 'client');

                $mform->addElement('password', 'password_confirmation', get_string('password'), 'maxlength="32" size="12"');
                $mform->setType('password_confirmation', core_user::get_property_type('password'));
                $mform->addRule('password_confirmation', get_string('missingpassword'), 'required', null, 'client');

                $mform->addElement('hidden', 'type');
                $mform->setDefault('type', 'forgotten_password');
            } else {
                $mform->addElement('header', 'activationcodeheader', get_string('activation_code', 'auth_sms'), '');

                $mform->addElement('text', 'activation_code', get_string('activation_code', 'auth_sms'), 'maxlength="100" size="12" autocapitalize="none"');
                $mform->setType('activation_code', PARAM_RAW);
                $mform->addRule('activation_code', '', 'required', null, 'client');

                $mform->addElement('header', 'createuserandpass', get_string('createuserandpass'), '');

                $mform->addElement('text', 'username', get_string('username'), 'maxlength="100" size="12" autocapitalize="none"');
                $mform->setType('username', PARAM_RAW);
                $mform->addRule('username', get_string('missingusername'), 'required', null, 'client');

                if (!empty($CFG->passwordpolicy)){
                    $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
                }
                $mform->addElement('password', 'password', get_string('password'), 'maxlength="32" size="12"');
                $mform->setType('password', core_user::get_property_type('password'));
                $mform->addRule('password', get_string('missingpassword'), 'required', null, 'client');

                $mform->addElement('header', 'supplyinfo', get_string('supplyinfo'),'');

                $mform->addElement('hidden', 'email');
                $mform->setType('email', core_user::get_property_type('email'));

                $mform->addElement('hidden', 'email2');
                $mform->setType('email2', core_user::get_property_type('email'));

                $namefields = useredit_get_required_name_fields();
                foreach ($namefields as $field) {
                    $mform->addElement('text', $field, get_string($field), 'maxlength="100" size="30"');
                    $mform->setType($field, core_user::get_property_type('firstname'));
                    $stringid = 'missing' . $field;
                    if (!get_string_manager()->string_exists($stringid, 'moodle')) {
                        $stringid = 'required';
                    }
                    $mform->addRule($field, get_string($stringid), 'required', null, 'client');
                }

                $mform->addElement('hidden', 'type');
                $mform->setDefault('type', 'signup');
            }
        }

        if (signup_captcha_enabled()) {
            $mform->addElement('recaptcha', 'recaptcha_element', get_string('security_question', 'auth'));
            $mform->addHelpButton('recaptcha_element', 'recaptcha', 'auth');
            $mform->closeHeaderBefore('recaptcha_element');
        }

        // buttons
        $this->add_action_buttons(true, get_string('submit'));
    }

    function definition_after_data(){
        global $SESSION;
        $mform = $this->_form;
        if(!isset($SESSION->activation_code) || !$SESSION->activation_code) {
        } else {
            $mform->applyFilter('username', 'trim');
            $email = auth_sms_random_number() . '@' . time() . '.ir';
            $mform->setDefault('email', $email);
            $mform->setDefault('email2', $email);
        }
    }

     /**
     * Validate user supplied data on the signup form.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        global $SESSION, $DB;
        $errors = parent::validation($data, $files);
        if (signup_captcha_enabled()) {
            $recaptchaelement = $this->_form->getElement('recaptcha_element');
            if (!empty($this->_form->_submitValues['g-recaptcha-response'])) {
                $response = $this->_form->_submitValues['g-recaptcha-response'];
                if (!$recaptchaelement->verify($response)) {
                    $errors['recaptcha_element'] = get_string('incorrectpleasetryagain', 'auth');
                }
            } else {
                $errors['recaptcha_element'] = get_string('missingrecaptchachallengefield');
            }
        }
        if($data['type'] == 'phone2') {
            if(isset($data['forgotten_password']) && $data['forgotten_password']) {
                // check mobile exist (mobile must be exist)
                if(true) {
                    $phone2 = substr($data['phone2'], -10, 10); //09123456789 and +989123456789 is the same;
                    $phone_place = auth_sms_get_phone_place();
                    if($phone_place == 'phone2') {
                        $params = array(
                            'phone2' => '%' . $phone2, //09123456789 and +989123456789 is the same
                        );
                        if (!$DB->get_record_sql("SELECT * FROM {user} WHERE phone2 LIKE :phone2 AND deleted <> 1", $params)) {
                            $errors['phone2'] = get_string('not_exist_error', 'auth_sms');
                        }
                    } else {
                        $params = array(
                            'phone2' => '%' . $phone2, //09123456789 and +989123456789 is the same
                            'fieldid' => $phone_place,
                        );
                        if (!$DB->get_record_sql("SELECT * FROM {user} u RIGHT JOIN {user_info_data} uid ON u.id = uid.userid WHERE uid.fieldid = :fieldid AND data LIKE :phone2 AND u.deleted <> 1", $params)) {
                            $errors['phone2'] = get_string('not_exist_error', 'auth_sms');
                        }
                    }
                }
                //send sms and check errors in sending
                if(!isset($errors['phone2'])) {
                    $SESSION->forgotten_password = true;
                    $errors += $this->send($data, $errors);
                }
            } else {
                // check mobile exist (mobile must not be exist)
                if(true) {
                    $phone2 = substr($data['phone2'], -10, 10); //09123456789 and +989123456789 is the same;
                    $phone_place = auth_sms_get_phone_place();
                    if($phone_place == 'phone2') {
                        $params = array(
                            'phone2' => '%' . $phone2, //09123456789 and +989123456789 is the same
                        );
                        if ($DB->get_record_sql("SELECT * FROM {user} WHERE phone2 LIKE :phone2 AND deleted <> 1", $params)) {
                            $errors['phone2'] = get_string('phone2exists', 'auth_sms');
                        }
                    } else {
                        $params = array(
                            'phone2' => '%' . $phone2, //09123456789 and +989123456789 is the same
                            'fieldid' => $phone_place,
                        );
                        if ($DB->get_record_sql("SELECT * FROM {user} u RIGHT JOIN {user_info_data} uid ON u.id = uid.userid WHERE uid.fieldid = :fieldid AND data LIKE :phone2 AND u.deleted <> 1", $params)) {
                            $errors['phone2'] = get_string('phone2exists', 'auth_sms');
                        }
                    }
                }
                //send sms and check errors in sending
                if(!isset($errors['phone2'])) {
                    $errors += $this->send($data, $errors);
                }
            }
        } else if($data['type'] == 'signup') {
            $errors += signup_validate_data($data, $files);
        } else if($data['type'] == 'forgotten_password') {
            $errmsg = '';
            if (!check_password_policy($data['password'], $errmsg)) {
                $errors['password'] = $errmsg;
            }
        }
        if(isset($SESSION->activation_code) && $SESSION->activation_code) {
            if(isset($data['activation_code']) && $data['activation_code'] != $SESSION->activation_code) {
                $errors['activation_code'] = get_string('invalid_activation_code', 'auth_sms');
            }
        }
        if(isset($data['password']) && isset($data['password_confirmation']) && $data['password'] != $data['password_confirmation']) {
            $errors['password'] = get_string('password_mismatch_error', 'auth_sms');
        }
        return $errors;
    }

    public function send($data, $errors) {
        global $SESSION;
        $config = get_config('auth_sms');

        $data = (object) $data;
        $SESSION->activation_code = auth_sms_random_number();
        $SESSION->phone2 = $data->phone2;
        //
        if(!$config->smstype || empty($config->smstype) || $config->smstype == 'sms.ir') {
            $APIKey = $config->smsapikey;
            $SecretKey = $config->smssecretkey;
            $Code = $SESSION->activation_code;
            $MobileNumber = $data->phone2;
            $template_id = $config->smstemplateid;
            if($SecretKey && !empty($SecretKey)) { //method 1
                $SmsIR_VerificationCode = new SmsIR_VerificationCode($APIKey, $SecretKey);
                $VerificationCode = $SmsIR_VerificationCode->verificationCode($Code, $MobileNumber);
            } else if($template_id && !empty($template_id)) { //method 2
                smsir_send_verification_code($APIKey, $template_id, $MobileNumber, $Code);
            } else {
                auth_sms_clear_session();
                $errors['phone2'] = get_string('wrong_settings', 'auth_sms');
            }
        } else if($config->smstype == 'magfa.com') {
            $username = $config->smsmagfausername;
            $password = $config->smsmagfapassword;
            $domain = $config->smsmagfadomain;
            $line_number = $config->smsmagfalinenumber;
            $result = magfa_send($username, $password, $domain, [auth_sms_get_template_text($SESSION->activation_code)], [$line_number], [$SESSION->phone2]);
            if($result && isset($result['send'])) {
                if($result['send']->status === 0 || $result['send']->status === '0') {
                    if($result['send']->messages) {
                        if($result['send']->messages->status === 0 || $result['send']->messages->status === '0') {
                            //it is ok.
                        } else {
                            auth_sms_clear_session();
                            $errors['phone2'] = get_string('error', 'core') . ': ' . $result['send']->messages->status;
                        }
                    } else {
                        auth_sms_clear_session();
                        print_error(print_r($result, true));
                        exit;
                    }
                } else {
                    auth_sms_clear_session();
                    print_error(print_r($result, true));
                    exit;
                }
            } else {
                auth_sms_clear_session();
                print_error(print_r($result, true));
                exit;
            }
        } else {
            auth_sms_clear_session();
            $errors['phone2'] = get_string('wrong_settings', 'auth_sms');
        }
        return $errors;
    }


    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        ob_start();
        $this->display();
        $formhtml = ob_get_contents();
        ob_end_clean();
        $context = [
            'formhtml' => $formhtml
        ];
        return $context;
    }
}
