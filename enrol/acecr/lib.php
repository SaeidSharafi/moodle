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
 * Acecr enrolment plugin.
 *
 * This plugin allows you to set up paid courses.
 *
 * @package    enrol_acecr
 * @copyright  2016 Hossein Harandipour
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
/**
 * Acecr enrolment plugin implementation.
 * @author  Hossein Harandipour - based on code by Martin Dougiamas and others
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_acecr_plugin extends enrol_plugin {
    
   function __construct() {
        require_once 'nusoap_client.php' ;
    }

    public function get_currencies() {
        // See https://www.mellat.com/cgi-bin/webscr?cmd=p/sell/mc/mc_intro-outside,
        // 3-character ISO-4217: https://cms.mellat.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_currency_codes
        $codes = array('IRR');
        $currencies = array();
        foreach ($codes as $c) {
            $currencies[$c] = new lang_string($c, 'core_currencies');
        }

        return $currencies;
    }

    /**
     * Returns optional enrolment information icons.
     *
     * This is used in course list for quick overview of enrolment options.
     *
     * We are not using single instance parameter because sometimes
     * we might want to prevent icon repetition when multiple instances
     * of one type exist. One instance may also produce several icons.
     *
     * @param array $instances all enrol instances of this type in one course
     * @return array of pix_icon
     */
    public function get_info_icons(array $instances) {
        $found = false;
        foreach ($instances as $instance) {
            if ($instance->enrolstartdate != 0 && $instance->enrolstartdate > time()) {
                continue;
            }
            if ($instance->enrolenddate != 0 && $instance->enrolenddate < time()) {
                continue;
            }
            $found = true;
            break;
        }
        if ($found) {
            return array(new pix_icon('icon', get_string('pluginname', 'enrol_acecr'), 'enrol_acecr'));
        }
        return array();
    }

    public function roles_protected() {
        // users with role assign cap may tweak the roles later
        return false;
    }

    public function allow_unenrol(stdClass $instance) {
        // users with unenrol cap may unenrol other users manually - requires enrol/acecr:unenrol
        return true;
    }

    public function allow_manage(stdClass $instance) {
        // users with manage cap may tweak period and status - requires enrol/acecr:manage
        return true;
    }

    public function show_enrolme_link(stdClass $instance) {
        return ($instance->status == ENROL_INSTANCE_ENABLED);
    }

    /**
     * Sets up navigation entries.
     *
     * @param object $instance
     * @return void
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {
        if ($instance->enrol !== 'acecr') {
             throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/acecr:config', $context)) {
            $managelink = new moodle_url('/enrol/acecr/edit.php', array('courseid'=>$instance->courseid, 'id'=>$instance->id));
            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }
    }

    /**
     * Returns edit icons for the page with list of instances
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'acecr') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid);

        $icons = array();

        if (has_capability('enrol/acecr:config', $context)) {
            $editlink = new moodle_url("/enrol/acecr/edit.php", array('courseid'=>$instance->courseid, 'id'=>$instance->id));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('t/edit', get_string('edit'), 'core',
                    array('class' => 'iconsmall')));
        }

        return $icons;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/acecr:config', $context)) {
            return NULL;
        }

        // multiple instances supported - different cost for different roles
        return new moodle_url('/enrol/acecr/edit.php', array('courseid'=>$courseid));
    }

    /**
     * Creates course enrol form, checks if form submitted
     * and enrols user if necessary. It can also redirect.
     *
     * @param stdClass $instance
     * @return string html text, usually a form in a text box
     */
    function enrol_page_hook(stdClass $instance) {
        global $CFG, $USER, $OUTPUT, $PAGE, $DB;

        ob_start();

        if ($DB->record_exists('user_enrolments', array('userid'=>$USER->id, 'enrolid'=>$instance->id))) {
            return ob_get_clean();
        }

        if ($instance->enrolstartdate != 0 && $instance->enrolstartdate > time()) {
            return ob_get_clean();
        }

        if ($instance->enrolenddate != 0 && $instance->enrolenddate < time()) {
            return ob_get_clean();
        }

        $course = $DB->get_record('course', array('id'=>$instance->courseid));
        $context = context_course::instance($course->id);

        $shortname = format_string($course->shortname, true, array('context' => $context));
        $strloginto = get_string("loginto", "", $shortname);
        $strcourses = get_string("courses");

        // Pass $view=true to filter hidden caps if the user cannot see them
        if ($users = get_users_by_capability($context, 'moodle/course:update', 'u.*', 'u.id ASC',
                                             '', '', '', '', false, true)) {
            $users = sort_by_roleassignment_authority($users, $context);
            $teacher = array_shift($users);
        } else {
            $teacher = false;
        }

        if ( (float) $instance->cost <= 0 ) {
            $cost = (float) $this->get_config('cost');
        } else {
            $cost = (float) $instance->cost;
        }

        if (abs($cost) < 1000) { // no cost, other enrolment methods (instances) should be used
            echo '<p>'.get_string('nocost', 'enrol_acecr').'</p>';
        } else {

            // Calculate localised and "." cost, make sure we send AcecrPayment the same value,
            // please note AcecrPayment expects amount with 2 decimal places and "." separator.
            $localisedcost = format_float($cost, 0, true);
            $cost = format_float($cost, 0, false);

            if (isguestuser()) { // force login only for guest user, not real users with guest role
                if (empty($CFG->loginhttps)) {
                    $wwwroot = $CFG->wwwroot;
                } else {
                    // This actually is not so secure ;-), 'cause we're
                    // in unencrypted connection...
                    $wwwroot = str_replace("http://", "https://", $CFG->wwwroot);
                }
                echo '<div class="mdl-align"><p>'.get_string('paymentrequired').'</p>';
                echo '<p><b>'.get_string('cost').": $localisedcost".' ریال </b></p>';
                echo '<p><a href="'.$wwwroot.'/login/">'.get_string('loginsite').'</a></p>';
                echo '</div>';
            } else {
            //$orderid = rand(100000,999999);
            isset($_POST['order_id']) ? $ordid = $_POST['order_id'] : $ordid = 0 ;
            $transaction = (object) array(
                'courseid' => $course->id,
                'userid' => $USER->id,
                'email' => $USER->email,
                'instanceid' => $instance->id,
                'price' => $cost,
                'refid' => '',
                'randid' => $ordid,
                'payment_status' => '',
                'username' => fullname($USER),
                'courcename' => $course->fullname,
                'timeupdated' => time(),
            );

                if(isset($_POST["acecr"])) {
                    $DB->insert_record("enrol_acecr", $transaction);
                }
                include($CFG->dirroot.'/enrol/acecr/enrol.php');
            }

        }

        return $OUTPUT->box(ob_get_clean());
    }

    /**
     * Restore instance and map settings.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $course
     * @param int $oldid
     */
    public function restore_instance(restore_enrolments_structure_step $step, stdClass $data, $course, $oldid) {
        global $DB;
        if ($step->get_task()->get_target() == backup::TARGET_NEW_COURSE) {
            $merge = false;
        } else {
            $merge = array(
                'courseid'   => $data->courseid,
                'enrol'      => $this->get_name(),
                'roleid'     => $data->roleid,
                'cost'       => $data->cost,
                'currency'   => $data->currency,
            );
        }
        if ($merge and $instances = $DB->get_records('enrol', $merge, 'id')) {
            $instance = reset($instances);
            $instanceid = $instance->id;
        } else {
            $instanceid = $this->add_instance($course, (array)$data);
        }
        $step->set_mapping('enrol', $oldid, $instanceid);
    }

    /**
     * Restore user enrolment.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $instance
     * @param int $oldinstancestatus
     * @param int $userid
     */
    public function restore_user_enrolment(restore_enrolments_structure_step $step, $data, $instance, $userid, $oldinstancestatus) {
        $this->enrol_user($instance, $userid, null, $data->timestart, $data->timeend, $data->status);
    }

    /**
     * Gets an array of the user enrolment actions
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue A user enrolment object
     * @return array An array of user_enrolment_actions
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        if ($this->allow_unenrol($instance) && has_capability("enrol/acecr:unenrol", $context)) {
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'), $url, array('class'=>'unenrollink', 'rel'=>$ue->id));
        }
        if ($this->allow_manage($instance) && has_capability("enrol/acecr:manage", $context)) {
            $url = new moodle_url('/enrol/editenrolment.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/edit', ''), get_string('edit'), $url, array('class'=>'editenrollink', 'rel'=>$ue->id));
        }
        return $actions;
    }

    public function cron() {
        $trace = new text_progress_trace();
        $this->process_expirations($trace);
    }

    /**
     * Execute synchronisation.
     * @param progress_trace $trace
     * @return int exit code, 0 means ok
     */
    public function sync(progress_trace $trace) {
        $this->process_expirations($trace);
        return 0;
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/acecr:config', $context);
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/acecr:config', $context);
    }
    public function startPayment($amount, $callBackUrl)
    {         
        $terminal = $this->get_config('terminal');

        $curl = curl_init();
        $field = [
            "returnurl"      => $callBackUrl,
            "description"    => '',
            "price"          => $amount,
            "orderId"        => $_POST["order_id"],
            "additionaldata" => '',
            "merchantID"     => "Integrated:{$terminal}"
        ];
        curl_setopt_array($curl, array(
            CURLOPT_URL            => 'https://pay.acecr.ac.ir/Mellat/ProceedPayment',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($field, JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json'
            ),
        ));
        $result = curl_exec($curl);
        if (preg_match("/\('([^']+)'/", $result, $matches)) {
            $ref = $matches[1];
        }else{
            echo '<h2>Fault</h2><pre>';
            print_r($result);
            echo '</pre>';
            die();
        }

        $this->postRefId($ref);

    }

    public function verifyPayment($params) 
    {
        $terminal = $this->get_config('terminal');
        $orderId = $params["SaleOrderId"];
        $saleReferenceId = $params['SaleReferenceId'];
        $resCode = $params["ResCode"];
        $price = $params["price"];
        $field = [
            "saleOrderId"     => $orderId,
            "saleReferenceId" => $saleReferenceId,
            "resCode"         => $resCode,
            "amountR"         => $price."",
        ];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => 'https://pay.acecr.ac.ir/Mellat/TrasnactionisValid',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($field, JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json'
            ),
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result, true, 512, JSON_THROW_ON_ERROR)['item1'];

        if ($result !== true) {
            echo '<h2>Fault</h2><pre>';
            print_r($result);
            echo '</pre>';
            die();
        }

        return true;
    }
    
    public function settlePayment($params) 
    {
        $terminal = $this->get_config('terminal');
        $orderId = $params["SaleOrderId"];
        $saleReferenceId = $params['SaleReferenceId'];
        $resCode = $params["ResCode"];
        $price = $params["price"];
        $field = [
            "saleOrderId"     => $orderId,
            "saleReferenceId" => $saleReferenceId,
            "resCode"         => $resCode,
            "amountR"         => $price."",
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => 'https://pay.acecr.ac.ir/Mellat/PayElectronically',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($field, JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json'
            ),
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result, true, 512, JSON_THROW_ON_ERROR)['item1'];
        if ($result !== true) {
            echo '<h2>Fault</h2><pre>';
            print_r($result);
            echo '</pre>';
            die();
        }

        return true;
    }
    
    public function checkPayment($params,$transaction)
    {

        $params["RefId"] = $params["RefId"] ;
        $params["SaleOrderId"] = $params["SaleOrderId"] ;
        $params["SaleReferenceId"] = $params["SaleReferenceId"] ;
        $params["ResCode"] = $params["ResCode"] ;
        $params["price"] = $transaction->price ;
        if( $params["ResCode"] == 0 )
        {
            if( $this->verifyPayment($params) == true ) {

                if( $this->settlePayment($params) == true ) {
                    return array(
                        "status"=>"success",
                        "msg" => get_string('success','enrol_acer'),
                        "trans"=>$params["SaleReferenceId"]
                    );
                }
            }
        }
        return array(
            "status"=>"failed",
            "msg" => $this->fault($params["ResCode"]),
            "trans"=>$params["SaleReferenceId"]
        );
    }   

    protected function fault($code){
        switch ($code) {
            case '11':
                $message = get_string('e11', 'enrol_acecr');
                break;

            case '12':
                $message = get_string('e12', 'enrol_acecr');
                break;

            case '13':
                $message = get_string('e13', 'enrol_acecr');
                break;

            case '14':
                $message = get_string('e14', 'enrol_acecr');
                break;

            case '15':
                $message = get_string('e15', 'enrol_acecr');
                break;

            case '16':
                $message = get_string('e16', 'enrol_acecr');
                break;

            case '17':
                $message = get_string('e17', 'enrol_acecr');
                break;

            case '18':
                $message = get_string('e18', 'enrol_acecr');
                break;

            case '19':
                $message = get_string('e19', 'enrol_acecr');
                break;

            case '111':
                $message = get_string('e111', 'enrol_acecr');
                break;

            case '112':
                $message = get_string('e112', 'enrol_acecr');
                break;

            case '113':
                $message = get_string('e113', 'enrol_acecr');
                break;

            case '114':
                $message = get_string('e114', 'enrol_acecr');
                break;

            case '21':
                $message = get_string('e21', 'enrol_acecr');
                break;

            case '23':
                $message = get_string('e23', 'enrol_acecr');
                break;

            case '24':
                $message = get_string('e24', 'enrol_acecr');
                break;

            case '25':
                $message = get_string('e25', 'enrol_acecr');
                break;

            case '31':
                $message = get_string('e31', 'enrol_acecr');
                break;

            case '32':
                $message = get_string('e32', 'enrol_acecr');
                break;

            case '33':
                $message = get_string('e33', 'enrol_acecr');
                break;

            case '34':
                $message = get_string('e34', 'enrol_acecr');
                break;

            case '35':
                $message = get_string('e35', 'enrol_acecr');
                break;

            case '41':
                $message = get_string('e41', 'enrol_acecr');
                break;

            case '42':
                $message = get_string('e42', 'enrol_acecr');
                break;

            case '43':
                $message = get_string('e43', 'enrol_acecr');
                break;

            case '44':
                $message = get_string('e44', 'enrol_acecr');
                break;

            case '45':
                $message = get_string('e45', 'enrol_acecr');
                break;

            case '46':
                $message = get_string('e46', 'enrol_acecr');
                break;

            case '47':
                $message = get_string('e47', 'enrol_acecr');
                break;

            case '48':
                $message = get_string('e48', 'enrol_acecr');
                break;

            case '49':
                $message = get_string('e49', 'enrol_acecr');
                break;

            case '412':
                $message = get_string('e412', 'enrol_acecr');
                break;

            case '413':
                $message = get_string('e413', 'enrol_acecr');
                break;

            case '414':
                $message = get_string('e414', 'enrol_acecr');
                break;

            case '415':
                $message = get_string('e415', 'enrol_acecr');
                break;

            case '416':
                $message = get_string('e416', 'enrol_acecr');
                break;

            case '417':
                $message = get_string('e417', 'enrol_acecr');
                break;

            case '418':
                $message = get_string('e418', 'enrol_acecr');
                break;

            case '419':
                $message = get_string('e419', 'enrol_acecr');
                break;

            case '421':
                $message = get_string('e421', 'enrol_acecr');
                break;

            case '51':
                $message = get_string('e51', 'enrol_acecr');
                break;

            case '54':
                $message = get_string('e54', 'enrol_acecr');
                break;

            case '55':
                $message = get_string('e55', 'enrol_acecr');
                break;

            case '61':
                $message = get_string('e61', 'enrol_acecr');
                break;

            default :
                if (!empty($error)) {
                    $message = $error;
                } else {
                    $message = get_string('ex', 'enrol_acecr');
                }
                break;
        }
        return $message;
    }
    protected function postRefId($refIdValue) 
    {
        echo '<script language="javascript" type="text/javascript"> 
                function postRefId (refIdValue) {
                var form = document.createElement("form");
                form.setAttribute("method", "POST");
                form.setAttribute("action", "https://bpm.shaparak.ir/pgwchannel/startpay.mellat");         
                form.setAttribute("target", "_self");
                var hiddenField = document.createElement("input");              
                hiddenField.setAttribute("name", "RefId");
                hiddenField.setAttribute("value", refIdValue);
                form.appendChild(hiddenField);
    
                document.body.appendChild(form);         
                form.submit();
                document.body.removeChild(form);
            }
            postRefId("' . $refIdValue . '");
            </script>';
    }
    
    protected function error($number) 
    {
        $err = $this->response($number);
        echo '<!doctype html><html><head><meta charset="utf-8"><title>خطا</title></head><body dir="rtl">';
        echo '<style>div.error{direction:rtl;background:#A80202;float:right;text-align:right;color:#fff;';
        echo 'font-family:tahoma;font-size:13px;padding:3px 10px}</style>';
        echo '<div class="error"><strong>خطا</strong> : ' . $err . '</div>';
        die ;
    }
    
    protected function response($number) 
    {
        switch($number) {
            case 31 :
                $err = "پاسخ نامعتبر است!"; 
                break;
            case 17 :
                $err = "کاربر از انجام تراکنش منصرف شده است!";
                break;
            case 21 :
                $err = "پذیرنده نامعتبر است!";
                break;
            case 25 :
                $err = "مبلغ نامعتبر است!";
                break;
            case 34 :
                $err = "خطای سیستمی!";
                break;
            case 41 :
                $err = "شماره درخواست تکراری است!";
                break;
            case 421 :
                $err = "ای پی نامعتبر است!";
                break;
            case 412 :
                $err = "شناسه قبض نادرست است!";
                break;
            case 45 :
                $err = "تراکنش از قبل ستل شده است";
                break;
            case 46 :
                $err = "تراکنش ستل شده است";
                break;
            case 35 :
                $err = "تاریخ نامعتبر است";
                break;
            case 32 :
                $err = "فرمت اطلاعات وارد شده صحیح نمیباشد";
                break;
            case 43 :
                $err = "درخواست verify قبلا صادر شده است";
                break;
            
        }
        return $err ;
    }
}
