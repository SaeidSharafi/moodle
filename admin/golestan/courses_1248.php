<?php
set_time_limit(-1);
include_once "utils.php";
include_once "settings.php";
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
        echo json_encode(array('success' => 0, 'msg' => 'اطلاعات وارد شده ناقص می باشد'), JSON_UNESCAPED_UNICODE);
        return;
    }
    set_time_limit(-1);
    $center = $_POST['center'] ?: 10;
    $params = $_POST['params'];
    $term = $params['term'] ?: '4002';
    $crs_number = $params['course'] ?: null;
    $edu_group = $params['edu_group'] ?: null;
    $crs_group = $params['crs_group'] ?: null;
    $crs_degree = $params['crs_degree'] ?: null;
    $crs_degree = $params['crs_degree'] ?: null;

    $client = new SoapClient("{$CFG->golestan_url}/GolestanService/gservice.asmx?WSDL");
    $pub = "<Root>";
    $pub .= create_pub(Courses_1248::TERM, $term);
    $pub .= create_pub(Courses_1248::STATE,37);
    $pub .= create_pub(Courses_1248::COURSE_STATE, "1");

    if (is_array($center)){
        $pub .= create_pub(Courses_1248::CENTER,$center[0],$center[0]);
        if (count($center) > 1 && $center[1]){
            $pub .= create_pub(Courses_1248::UNIVERSITY, $center[1], $center[1]);
        }

    }else{
        $pub .= create_pub(Courses_1248::CENTER, $center, $center);

    }
    if ($edu_group) {
        $grps = explode("-",$edu_group);
        if (count($grps) > 1){
            $pub .= create_pub(Courses_1248::GROUP, $grps[0],$grps[1]);
        }else{
            $pub .= create_pub(Courses_1248::GROUP, $edu_group);
        }

    }
    if ($crs_number) {
        $grps = explode("-",$crs_number);
        if (count($grps) > 1){
            $pub .= create_pub(Courses_1248::COURSE_NUMBER, $grps[0],$grps[1]);
        }else {

            $pub .= create_pub(Courses_1248::COURSE_NUMBER, $crs_number);
        }
    }
    if ($crs_group) {
        $grps = explode("-",$crs_group);
        if (count($grps) > 1){
            $pub .= create_pub(Courses_1248::COURSE_GROUP, $grps[0],$grps[1]);
        }else {
            $pub .= create_pub(Courses_1248::COURSE_GROUP, $crs_group);
        }
    }
    if ($crs_degree) {
        $grps = explode("-",$crs_degree);
        if (count($grps) > 1){
            $pub .= create_pub(Courses_1248::COURSE_DEGREE, $grps[0],$grps[1]);
        }else{
            $pub .= create_pub(Courses_1248::COURSE_DEGREE, $crs_degree);
        }
    }
    $pub .= "</Root>";

    $pri = "<Root>";
    $pri .= create_pri(Courses_1248::PRI_PREREQUISITES_UQID, Courses_1248::PRI_PREREQUISITES_ID, 0);
    $pri .= create_pri(Courses_1248::PRI_COURSE_LIST_UQID, Courses_1248::PRI_COURSE_LIST_ID, 1);
    $pri .= "</Root>";

    $XmlInfo = $client->__soapCall('golInfo',
        array(array('login' => $CFG->golestan_user, 'pass' => $pass, 'sec' => 'F87E1A81B3', 'iFID' => '1248', 'pub' => $pub,
                    'pri' => $pri, 'mor' => '')));
    $xml = '<?xml version="1.0" encoding="utf-8"?>';
    $xml .= $XmlInfo->golInfoResult->any;

    $xml = simplexml_load_string($xml);
    $courses = array();
    if ($xml === false) {
        $msg = "Failed loading XML: \n";
        foreach (libxml_get_errors() as $error) {
            $msg .= "\n" . $error->message;
        }
        header('Content-Type: application/json');
        echo json_encode(array('success' => 0, 'msg' => $msg), JSON_UNESCAPED_UNICODE);
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
        foreach ($xml->row as $row) {
            //$item['crs'] = $row;
            $crs = explode("_", trim((string) $row['C9']));
            $crs_id = $crs[0];
            $crs_group = $crs[1];

            $item['state_id'] = trim((string) $row['C1']);
            $item['state_name'] = trim((string) $row['C2']);
            $item['center_id'] = trim((string) $row['C3']);
            $item['center_name'] = trim((string) $row['C4']);
            $item['college_id'] = trim((string) $row['C5']);
            $item['college_name'] = trim((string) $row['C6']);
            $item['group_id'] = trim((string) $row['C7']);
            $item['group_name'] = trim((string) $row['C8']);

            $item['id'] = $crs_id;
            $item['name'] = trim((string) $row['C10']);
            $item['group'] = $crs_group;
            $teacher = explode("-", trim((string) $row['C16']));
            $item['teacher_id'] = 't'.trim($teacher[0]);
            $item['teacher_name'] = trim(str_replace("<BR>", "", $teacher[1]));
            $item['exam_date'] = strip_tags(trim((string) $row['C18']));
            $item['exam_hour'] = trim((string) $row['C19']);
            $item['degree_id'] = trim((string) $row['C21']);
            $item['degree_name'] = trim((string) $row['C22']);
            $item['term'] = $term;
            array_push($courses, $item);
        }

        if (is_array($center)){
            $msg = "در حال  ثبت دوره های ارائه شده در مرکز شماره" . $center[0]." کد دانشکده " . $center[1];

        }else{
            $msg = "در حال  ثبت دوره های ارائه شده در مرکز شماره" . $center;
        }

        header('Content-Type: application/json');
        echo  json_encode(array('success' => 1, 'msg' => $msg, 'items' => $courses), JSON_UNESCAPED_UNICODE);

    }
}

init();

?>


