<?php
set_time_limit(-1);
require_once "settings.php";
include_once "utils.php";

function init()
{
    global $CFG;
    if (!isset($_POST['key'])) {
        echo json_encode(array('success' => 0, 'msg' => "رمز عبور وارد نشده است"), JSON_UNESCAPED_UNICODE);
        return;
    }
    $pass = $_POST['key'];


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
    $center = $_POST['center'] ?: [10,10];
    $term = $params['term'] ?: '4002';

    $client = new SoapClient("{$CFG->golestan_url}/GolestanService/gservice.asmx?WSDL");


    $pub = "<Root>";
    if (is_array($center)){
        $pub .= create_pub(Teachers_1131::CENTER,$center[0],$center[0]);
        if (count($center) > 1 && $center[1]){
            $pub .= create_pub(Teachers_1131::UNIVERSITY,$center[1],$center[1]);
        }
        if (count($center) > 2 && $center[2]){
            $pub .= create_pub(Teachers_1131::GROUP,$center[2],$center[2]);
        }

    }else{
        $pub .= create_pub(Teachers_1131::CENTER,$center);

    }
    $pub .= "</Root>";
    $pri = "<Root>";
    $pri .= create_pri(Teachers_1131::PRI_LETTER_UQID,Teachers_1131::PRI_LETTER_ID,0);
    $pri .= "</Root>";

    $XmlInfo =  $client->__soapCall( 'golInfo' ,
        array(array('login' => $CFG->golestan_user, 'pass' => $pass,'sec'=>'350CF3E436','iFID'=>'1131','pub'=>$pub,'pri'=>$pri,'mor'=>'')));
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
        if ($xml->e){
            header('Content-Type: application/json');
            $msg = '';
            foreach ($xml->e as $error) {
                $msg .= "\n" . $error;
            }
            echo json_encode(array('success' => 0,'msg' =>$msg),JSON_UNESCAPED_UNICODE );
            return;
        }
        foreach ($xml->p as $row) {
            $item['id'] = 't'.trim((string)$row['C1']);
            $item['fname'] = trim((string)$row['C3']);
            $item['lname'] = trim((string)$row['C4']);
            $item['meli'] = is_numeric(trim((string)$row['C5'])) ? trim((string)$row['C5']) :  trim((string)$row['C1']);
            $item['email'] = $item['id']."@pafco.ir";

            array_push($teachers, $item);

        }


        $msg = "در حال  ثبت اطلاعات اساتید مرکز شماره".$center[0] ." کد دانشکده " . $center[1] ;
        header('Content-Type: application/json');
        echo json_encode(array('success' => 1,'msg' =>$msg,'items' => $teachers),JSON_UNESCAPED_UNICODE );

    }
}

init();
?>
