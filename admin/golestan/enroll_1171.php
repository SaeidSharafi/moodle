<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 10/29/2020
 * Time: 1:41 PM
 */
include_once "utils.php";
include_once "settings.php";
function init()
{
    global $CFG;
    if (!isset($_POST['key']) || $_POST['key'] != Config::$security_key) {
        echo json_encode(array('success' => 0,'msg' => "کد امنیتی نامعتبر"));
        return;
    }
    set_time_limit(-1);
    if (!isset($_POST['action']) || !$_POST['action'] || !isset($_POST['center']) ||
        !$_POST['center'] || !isset($_POST['params']) || !$_POST['params']) {
        echo json_encode(array('success' => 0,'msg' => 'اطلاعات وارد شده ناقص می باشد'));
        return;
    }
    if ($_POST['action'] != 'enroll') {
        echo json_encode(array('success' => 0,'msg' => 'اطلاعات وارد شده ناقص می باشد'));
        return;
    }

    $params = $_POST['params'];
    $center = $_POST['center'] ? $_POST['center'] : 10;
    $term = $params['term'] ? $params['term'] : '4022';
    $client = new SoapClient("{$CFG->golestan_url}/GolestanService/gservice.asmx?WSDL");

    $pri = "<Root>";
    $pri .= create_pri(Enrollment_1171::PRI_TERM_UQID,Enrollment_1171::PRI_TERM_ID, $term,$term);
    $pri .= create_pri(Enrollment_1171::PRI_STATE_UQID,Enrollment_1171::PRI_STATE_ID,37,37);

    if (is_array($center)){
        $pri .= create_pri(Enrollment_1171::PRI_CENTER_UQID,Enrollment_1171::PRI_CENTER_ID,$center[0],$center[0]);
        if (count($center) > 1 && $center[1]){
            $pri .= create_pri(Enrollment_1171::PRI_UNIVERSITY_UQID,Enrollment_1171::PRI_UNIVERSITY_ID,$center[1],$center[1]);
        }
    }else{
        $pri .= create_pri(Enrollment_1171::PRI_CENTER_UQID,Enrollment_1171::PRI_CENTER_ID, $center, $center);
    }
    //$pri .= create_pri(240,10, 10, 10);
    $pri .= create_pri(Enrollment_1171::PRI_SOURCE_UQID,Enrollment_1171::PRI_SOURCE_ID, 1,1);
    $pri .= create_pri(Enrollment_1171::PRI_SHOW_INTRO_TEACHERS_UQID,Enrollment_1171::PRI_SHOW_INTRO_TEACHERS_ID, 0,0);
    $pri .= create_pri(Enrollment_1171::PRI_SHOW_PROJECT_COURSES_UQID,Enrollment_1171::PRI_SHOW_PROJECT_COURSES_ID, 0,0);
    $pri .= "</Root>";



    $StudentInfo = $client->__soapCall('golInfo',
        array(array('login' => $CFG->golestan_user, 'pass' => $CFG->golestan_pass, 'sec' => '426591DD5A', 'iFID' => '1083', 'pub' => '', 'pri' => $pri, 'mor' => '')));
    $xml = '<?xml version="1.0" encoding="utf-8"?>';
    $xml .= $StudentInfo->golInfoResult->any;

    $xml = simplexml_load_string($xml);


    $enrollments = array();
    if ($xml === false) {
        $msg = "Failed loading XML: \n";
        foreach (libxml_get_errors() as $error) {
            $msg .= "\n" . $error->message;
        }
        //header('Content-Type: application/json');
        echo json_encode(array('success' => 0, 'msg' => $msg), JSON_UNESCAPED_UNICODE);
    } else {
        foreach ($xml->row as $row) {
            $item['user_id'] = trim((string)$row['C1']);
            $item['crs_id'] = trim((string)$row['C16']);
            $item['group'] = trim((string)$row['18']);
            if (is_array($center)){
                $item['center_id'] = $center[0];
                $item['college_id'] = $center[1];
            }else{
                $item['center_id'] = $center;
                $item['college_id'] = (string)$row['C4'];
            }

            $item['term'] = $term;
            array_push($enrollments, $item);
        }
    }

    if (is_array($center)){
        $msg = "در حال  ثبت نام دانشپذیران مرکز شماره" . $center[0]." کد دانشکده " . $center[1];

    }else{
        $msg = "در حال  ثبت نام دانشپذیران مرکز شماره" . $center;
    }
    header('Content-Type: application/json');
    echo json_encode(array('success' => 1, 'msg' => $msg, 'items' => $enrollments));
}

init();


