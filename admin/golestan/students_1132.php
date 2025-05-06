<?php
set_time_limit(-1);
include_once "utils.php";
include_once "settings.php";

if (!isset($_POST['key']) || $_POST['key'] != Config::$security_key){
    echo json_encode(array('success' => 0,'msg' => "کد امنیتی نامعتبر"));
    return;
}


if (!isset($_POST['action']) || !$_POST['action'] || !isset($_POST['params']) || !$_POST['params']) {
    echo json_encode(array('success' => 0,'msg' => 'اطلاعات وارد شده ناقص می باشد'));
    return;
}
if ($_POST['action'] != 'students') {
    echo json_encode(array('success' => 0,'msg' => 'اطلاعات وارد شده ناقص می باشد'));
    return;
}
$params = $_POST['params'];
$center = $_POST['center'] ? $_POST['center'] : 1;
$term = $params['term'] ? $params['term'] : '4002';

$client = new SoapClient('http://golestan.ibi.ac.ir/GolestanService/gservice.asmx?WSDL');

$pub = "<Root>";
$pub .= create_pub(Students_1132::SOURCE,1);
$pub .= create_pub(Students_1132::STD_SOURCE);
//$pub .= create_pub(Students_1132::STD_ID,"9720139001");
$pub .= create_pub(Students_1132::STD_ID);
//$pub .= create_pub(Students_1132::CENTER,$center,$center);
$pub .= "</Root>";


$pri = "<Root>";
$pri .= create_pri(Students_1132::PRI_LETTER_UQID,Students_1132::PRI_LETTER_ID,0);
$pri .= create_pri(Students_1132::PRI_TERM_UQID,Students_1132::PRI_TERM_ID,$term);
$pri .= create_pri(153,20);
$pri .= create_pri(17450,24);
//$pri .= create_pri(Students_1132::PRI_TERM_UQID,Students_1132::PRI_TERM_ID,$term);
$pri .= "</Root>";


$StudentInfo =  $client->__soapCall( 'golInfo' ,
	array(array('login'=>'pafco','pass'=>Config::$soap_pass,'sec'=>'AA0ECD9901','iFID'=>'1132','pub'=>$pub,'pri'=>$pri,'mor'=>'')));
$xml = '<?xml version="1.0" encoding="utf-8"?>';
$xml .= $StudentInfo->golInfoResult->any;

$xml = simplexml_load_string($xml);
$students = array();
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
        $item['gender'] = trim((string)$row['C4']);
        $item['meli'] = trim((string)$row['C5']);
        $item['college'] = trim((string)$row['C6']);
        $item['degree'] = trim((string)$row['C7']);
        $item['mobile'] = trim((string)$row['C8']);
        $item['email']= trim((string)$row['C9']);
        if (!filter_var($item['email'], FILTER_VALIDATE_EMAIL)) {
            $item['email'] = $item['id']."@pafco.ir";
        }
        // var_dump($item);
        array_push($students, $item);
        //echo '</pre>';
    }


    $msg = "در حال  ثبت اطلاعات دانشپذیران";
    header('Content-Type: application/json');
    echo json_encode(array('success' => 1,'msg' =>$msg,'items' => $students),JSON_UNESCAPED_UNICODE );

}
?>


