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
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');

/**
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_otp_external extends external_api
{
    /**
     * @return external_function_parameters
     */
    public static function send_otp_parameters() {
        return new external_function_parameters(
            array(
                'phone' => new external_value(PARAM_TEXT, 'phone'),
            )
        );
    }

    /**
     * @param $phone
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function send_otp($phone) {
        global $DB, $CFG;
        $params = array(
            'phone' => $phone,
        );
        // Validate the params.
        self::validate_parameters(self::send_otp_parameters(), $params);

        $fullphone = strval($phone);

        // check user exist and last otp time
        $sql = 'select * from {auth_otp_linked_login} where phone = ?';
        $data = $DB->get_record_sql($sql,[$phone]);
        $user = null;
        $sql = 'select * from {user} where phone1 = ? OR phone2 = ?';
        $user = $DB->get_record_sql($sql,[$phone,$phone]);
        if ($user){
            $user->auth= 'otp';
            $DB->set_field('user','auth','otp', ['id' => $user->id]);
        }
        if (!$data){
            if ($user){
                $data = new stdClass();
                $data->phone = $phone;
                $data->confirmtoken = null;
                $data->username =  $user->username;
                $data->otpcreated =  null;
                $data->fullphone =  $phone;
                $data->countrycode =  null;
                $DB->insert_record('auth_otp_linked_login', $data);
                $sql = 'select * from {auth_otp_linked_login} where phone = ?';
                $data = $DB->get_record_sql($sql,[$phone]);
            }
        }
        $otp = null;
        $currentdate = '';
        // alreadu exist
        if ($data) {
            if ($data->otpcreated) {
                $seconds = self::calculate_time_diffrence($data->otpcreated);
                // Otp exist not expired
                if ($seconds['invert'] == 1 && $seconds['seconds'] <= get_config('auth_otp', 'minrequestperiod')) {
                    $res = [
                        'otp' => $data->confirmtoken,
                        'otpdatetime' => $data->otpcreated
                    ];
                    $_SESSION['auth_otp']['credentials'] = [
                        'otp' => $data->confirmtoken,
                        'otpdatetime' => $currentdate,
                        'username' =>  $phone,
                        'realusername' => $data->username,
                    ];
                    $username = $data->username;
                    $status = 1;
                    $message = get_string('otpsentinfo', 'auth_otp');

                } else { // Already exist otp but expired
                    $smsstatus = self::call_otp_funcction($fullphone);
                    if ($smsstatus['status']) {
                        $currentdate = date("Y-m-d H:i:s");
                        $otp = $smsstatus['otp']; // Get otp from message response
                        // Create new user
                        $username = self::old_user_handle($phone, $otp)['username'];
                        $message = get_string('otpsentsuccess', 'auth_otp');
                        $status = 1;
                    } else {
                        $message = $smsstatus['message'];
                        $status = 0;
                    }
                }
            } else {
                $smsstatus = self::call_otp_funcction($fullphone);
                if ($smsstatus['status']) {
                    $currentdate = date("Y-m-d H:i:s");
                    $otp = $smsstatus['otp']; // get otp from message response
                    // create new user
                    $username = self::old_user_handle($phone, $otp)['username'];
                    $message = get_string('otpsentsuccess', 'auth_otp');
                    $status = 1;
                } else {
                    $message = $smsstatus['message'];
                    $status = 0;
                }
            }
            $data = [
                'phone' => $phone,
                'username' => $username,
                'otp' => $otp,
                'timeout' => $currentdate,
                'message' => $message,
                'success' => $status,
                'warnings' => []
            ];
            return $data;

        }

        return [
            'phone' => $phone,
            'username' => '',
            'otp' => $otp,
            'timeout' => $currentdate,
            'message' => get_string('error_user_not_found','auth_otp'),
            'success' => 0,
            'warnings' => []
        ];


    }

    /**
     * @return external_single_structure
     */
    public static function send_otp_returns() {
        return new external_single_structure(
            array(
                'phone' => new external_value(PARAM_TEXT, 'phone'),
                'username' => new external_value(PARAM_TEXT, 'username'),
                'otp' => new external_value(PARAM_TEXT, 'otp'),
                'timeout' => new external_value(PARAM_TEXT, 'timeout'),
                'message' => new external_value(PARAM_TEXT, 'Error Message'),
                'success' => new external_value(PARAM_INT, 'Success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Call Otp sender service
     *
     * @param $phone
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function call_otp_funcction($phone) {
        $otp = self::generate_otp();
        // if set aws credentials
        if (get_config('auth_otp', 'enablemagfa')
            && get_config('auth_otp', 'magfa_username')
            && get_config('auth_otp', 'magfa_password')
            && get_config('auth_otp', 'magfa_number')
        && get_config('auth_otp', 'magfa_templatetext')) {
            $username = get_config('auth_otp', 'magfa_username');
            $password = get_config('auth_otp', 'magfa_password');
            $number = get_config('auth_otp', 'magfa_number');
            $domain = get_config('auth_otp', 'magfa_domain');
            $template = get_config('auth_otp', 'magfa_templatetext');

            $message = preg_replace('/\{code\}/', $otp, $template);
            try {
                $status = \auth_otp\magfaservices::sendOtp($message, $phone, $username, $password, $number, $domain);
                if($status === 0 || $status === '0') {
                    return ['status' => true, 'otp' => $otp, 'message' => get_string('otpsentsuccess', 'auth_otp')];
                }
                self::remove_otp($phone);
                return ['status' => false, 'otp' => $otp, 'message' => get_string('otpsenterror_number', 'auth_otp') . $status];

            } catch (Exception $e) {
                return ['status' => false, 'otp' => $otp, 'message' => get_string('otpsenterror', 'auth_otp')];
            }

        }
        else {// No sms credential found
            return ["status" => false, 'otp' => '', 'message' => get_string('otpsenterror', 'auth_otp')];
        }
    }

    /**
     * @return int
     */
    public static function generate_otp()
    {
        $digits = 6;
        $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        return strval($otp);
    }

    /**
     * Create new user and otp
     *
     * @param $phone
     * @param $otp
     * @return array
     * @throws dml_exception
     */
//    public static function new_user_handle($phone, $otp)
//    {
//        global $DB;
//        $currentdate = date("Y-m-d H:i:s");
//        //Write a function to send otp to the user
////        $data = $DB->execute("INSERT INTO {auth_otp_linked_login} (phone,confirmtoken,username,otpcreated,fullphone) VALUES ('" . $phone . "'," . $otp . ",'" . $phone . "','" . $currentdate . "','" . $phone . "')");
//
//        $data = new stdClass();
//        $data->phone = $phone;
//        $data->confirmtoken = $otp;
//        $data->username =  $phone;
//        $data->otpcreated =  $currentdate;
//        $data->fullphone =  $phone;
//        $data->countrycode =  null;
//
//        $DB->insert_record('auth_otp_linked_login', $data);
//
//        $_SESSION['auth_otp']['credentials'] = [
//            'otp' => $otp,
//            'otpdatetime' => $currentdate,
//            'username' => $phone,
//            'realusername' => $phone,
//        ];
//        $authplugin = get_auth_plugin('otp');
//        $user = new stdClass();
//        $user->auth = 'otp';
//        $user->confirmed = 1;
//        $user->firstaccess = 0;
//        $user->timecreated = time();
//        $user->username = $phone;
//        $user->phone1 = $phone;
//        $user->firstname = '';
//        $user->lastname = '';
//        $user->password = '';
//        $user->mnethostid = 1;
//        $user->email = $phone . '@otp.com';
//
//        $authplugin->create_user($user);
//        return [
//            'phone' => $phone,
//            'username' => $phone,
//            'otpdatetime' => $currentdate,
//            'otp' => $otp
//        ];
//    }

    /**
     * Update Old user otp token
     *
     * @param $phone
     * @param $otp
     * @return array
     * @throws dml_exception
     */
    public static function old_user_handle($phone, $otp)
    {
        global $DB;
        $currentdate = date("Y-m-d H:i:s");
        $data = $DB->execute("UPDATE {auth_otp_linked_login} SET confirmtoken= " . $otp . ",otpcreated = '" . $currentdate . "' where phone = '" . $phone . "'");
        $sql = 'select * from {user} where phone1 = ? OR phone2 = ?';
        $user = $DB->get_record_sql($sql,[$phone,$phone]);
        $_SESSION['auth_otp']['credentials'] = [
            'otp' => $otp,
            'otpdatetime' => $currentdate,
            'username' => $phone,
            'realusername' =>  $user ? $user->username : $phone,
        ];
        return [
            'phone' => $phone,
            'username' => $user ? $user->username : $phone,
            'otpdatetime' => $currentdate,
            'otp' => $otp
        ];
    }

    /**
     * Calculate time diffrence between otp generated time to current time
     * @param $otpcreated
     * @return array
     * @throws Exception
     */
    public static function calculate_time_diffrence($otpcreated)
    {
        $start = new DateTime(date("Y-m-d H:i:s"));
        $end = new DateTime(date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($otpcreated))));
        $diff = $end->diff($start);
        $daysInSecs = $diff->format('%r%a') * 24 * 60 * 60;
        $hoursInSecs = $diff->h * 60 * 60;
        $minsInSecs = $diff->i * 60;
        $seconds = $daysInSecs + $hoursInSecs + $minsInSecs + $diff->s;
        return ['invert' => $diff->invert, 'seconds' => $seconds];
    }

    public static function remove_otp($phone) {
        global $DB;
        $data = $DB->get_record('auth_otp_linked_login', ['phone' => $phone], '*');
        $data->confirmtoken = null;
        $data->otpcreated = null;

        $DB->update_record('auth_otp_linked_login', $data);

        return true;
    }

}
