<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 10/29/2020
 * Time: 1:41 PM
 */
include_once "utils.php";
include_once "settings.php";
if (!isset($_POST['key']) && $_POST['key'] != Config::$security_key){
    echo json_encode(array('success' => 0,'msg' => "wrong security key"));
    return;
}
set_time_limit(-1);
if (!isset($_POST['action']) && !$_POST['action'] && !isset($_POST['center']) && !$_POST['center']) {
    echo json_encode(array('success' => 0));
    return;
}


$center = $_POST['center'] ? $_POST['center'] : 1;
$client = new SoapClient('http://golestan.ibi.ac.ir/GolestanService/gservice.asmx?WSDL');
$term = '3991';
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
$enrollments = array();
if ($xml === false) {
    echo "Failed loading XML: ";
    foreach (libxml_get_errors() as $error) {
        echo "<br>", $error->message;
    }
} else {

    foreach ($xml->p as $row) {
        //echo '<pre>';

        $item['std_id'] = (string)$row['C1'];
        $item['crs_id'] = $row['C1'] . $row['C2'] . $row['C3'];
        // var_dump($item);
        array_push($enrollments, $item);
        //echo '</pre>';
    }

}

//echo json_encode($xml);
$enrolls = json_encode($enrollments);
$msg = "Registering items in center with id of ".$_POST['center'];
echo json_encode(array('success' => 1,'msg' =>$msg,'items' => $enrollments));
