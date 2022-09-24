<?php
set_time_limit(-1);
require_once "config.php";
include_once "utils.php";
if (!isset($_POST['key']) || $_POST['key'] != Config::$security_key){
    echo json_encode(array('success' => 0,'msg' => "کد امنیتی نامعتبر"));
    return;
}

if (!isset($_POST['action']) || !$_POST['action'] || !isset($_POST['center']) ||
    !$_POST['center'] || !isset($_POST['params']) || !$_POST['params']) {
    //var_dump($_POST);
    echo json_encode(array('success' => 0,'msg' => 'اطلاعات وارد شده ناقص می باشد'));
    return;
}
if ($_POST['action'] != 'teachers') {
    echo json_encode(array('success' => 0));
    return;
}

$params = $_POST['params'];
$center = $_POST['center'] ? $_POST['center'] : 1;
$term = $params['term'] ? $params['term'] : '4002';

$client = new SoapClient('http://golestan.ibi.ac.ir/GolestanService/gservice.asmx?WSDL');


$pub = "<Root>";
$pub .= create_pub(Teachers_1131::CENTER,$center);
$pub .= "</Root>";


$pri = "<Root>";
$pri .= create_pri(Teachers_1131::PRI_LETTER_UQID,Teachers_1131::PRI_LETTER_ID,0);
$pri .= "</Root>";

$XmlInfo =  $client->__soapCall( 'golInfo' ,
array(array('login'=>'pafco','pass'=>Config::$soap_pass,'sec'=>'350CF3E436','iFID'=>'1131','pub'=>$pub,'pri'=>$pri,'mor'=>'')));
$xml = '<?xml version="1.0" encoding="utf-8"?>';
$xml .= $XmlInfo->golInfoResult->any;
//
$xml = simplexml_load_string($xml);

$teachers = array();
if ($xml === false) {
    $msg = "Failed loading XML: \n";
    foreach (libxml_get_errors() as $error) {
        $msg .= "\n" . $error->message;
    }
    header('Content-Type: application/json');
    echo json_encode(array('success' => 0,'msg' =>$msg),JSON_UNESCAPED_UNICODE );
} else {

    foreach ($xml->p as $row) {

        $item['id'] = trim((string)$row['C1']);
        $item['fname'] = trim((string)$row['C2']);
        $item['lname'] = trim((string)$row['C3']);
        $item['email'] = $item['id']."@pafco.ir";
        // var_dump($item);
        array_push($teachers, $item);
        //echo '</pre>';
    }


    $msg = "در حال  ثبت اطلاعات اساتید مرکز شماره".$center;
    header('Content-Type: application/json');
    echo json_encode(array('success' => 1,'msg' =>$msg,'items' => $teachers),JSON_UNESCAPED_UNICODE );

}
?>
