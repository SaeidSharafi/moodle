<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 10/29/2020
 * Time: 1:41 PM
 */
include_once "utils.php";
include_once "settings.php";
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
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
$center = $_POST['center'] ? $_POST['center'] : 1;
$term = $params['term'] ? $params['term'] : '4002';
$client = new SoapClient('http://golestan.ibi.ac.ir/GolestanService/gservice.asmx?WSDL');

$pub = "<Root>";
$pub .= create_pub(Enrollment_1171::SOURCE, 1);
$pub .= create_pub(Enrollment_1171::STD_ID);
$pub .= create_pub(Enrollment_1171::CENTER, $center, $center);
$pub .= "</Root>";


$pri = "<Root>";
$pri .= create_pri(Enrollment_1171::PRI_LETTER_UQID, Enrollment_1171::PRI_LETTER_ID, 0);
$pri .= create_pri(Enrollment_1171::PRI_TERM_UQID, Enrollment_1171::PRI_TERM_ID, $term);
$pri .= "</Root>";


$StudentInfo = $client->__soapCall('golInfo',
    array(array('login' => 'pafco', 'pass' => Config::$soap_pass, 'sec' => '357BC8C0FF', 'iFID' => '1171', 'pub' => $pub, 'pri' => $pri, 'mor' => '')));
$xml = '<?xml version="1.0" encoding="utf-8"?>';
$xml .= $StudentInfo->golInfoResult->any;

$xml = simplexml_load_string($xml);
//var_dump($xml);
$enrollments = array();
if ($xml === false) {
    $msg = "Failed loading XML: \n";
    foreach (libxml_get_errors() as $error) {
        $msg .= "\n" . $error->message;
    }
    header('Content-Type: application/json');
    echo json_encode(array('success' => 0, 'msg' => $msg), JSON_UNESCAPED_UNICODE);
} else {

    foreach ($xml->p as $row) {
        //echo '<pre>';

        $item['user_id'] = trim((string)$row['C1']);
        $item['crs_id'] = $row['C2'] . $row['C3'] . $row['C4'];
        $item['group'] = trim((string)$row['C5']);
        $item['center_id'] = trim((string)$row['C6']);
        $item['term'] = $term;
        if (!$item['center_id'])
            $item['center_id'] = $center;
        // var_dump($item);
        array_push($enrollments, $item);
        //echo '</pre>';
    }

}

//echo json_encode($xml);
$enrolls = json_encode($enrollments);
$msg = "در حال  ثبت نام دانشپذیران مرکز شماره".$center;
header('Content-Type: application/json');
echo json_encode(array('success' => 1, 'msg' => $msg, 'items' => $enrollments));
