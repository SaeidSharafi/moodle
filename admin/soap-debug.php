<?php

//define('CLI_SCRIPT', true);

require('../config.php');
require_once $CFG->dirroot . '/course/lib.php';
use core_course_category;

$client = new SoapClient('http://45.135.243.83/lms/lms.asmx?WSDL');
//$param = array('_AuthSoapHd'=>array('strUserName'=>'abbasi','strPassword'=>'p@fcoLMS'));

//$LessonsInfo =  $client->__soapCall( 'GetFctLmsLesson' ,array('parameters' => $param));
$LessonsInfo = $client->__soapCall('GetFctLmsLesson', array(array('PafToken' => 'a5swoh0t69Yc+dg528QlB5n5Qk8XXtk')));

$ListLessons = $LessonsInfo->GetFctLmsLessonResult->LmsLesson;


if (count($ListLessons) == 1) {
    $MainCategory = check_category($ListLessons->FacualtyName, 0);
    $UniName = check_category($ListLessons->UniName, $MainCategory);
    $ListLessons->Categoryid = check_category($ListLessons->Category, $UniName);
    check_lesson($ListLessons);
    unset($ListLessons);
} else {
    foreach ($ListLessons AS $Lesson) {
        ob_implicit_flush(true);
        ob_start();
        $MainCategory = check_category($Lesson->FacualtyName, 0);
        $UniName = check_category($Lesson->UniName, $MainCategory);
        $Lesson->Categoryid = check_category($Lesson->Category, $UniName);
        check_lesson($Lesson);
        $res = check_lesson($Lesson);
        echo "<pre>";
        var_dump($res);
        echo "</pre>";
        if($res == false){
            unset($Lesson);
            ob_flush();
            ob_end_flush();
            break;
        }else{

            echo "<pre>";
            var_dump($Lesson);
            echo "</pre>";
        }
        unset($Lesson);
        ob_flush();
        ob_end_flush();
    }
}

function check_category($catname, $parent)
{
    global $DB, $CFG;
    $category = $DB->get_record('course_categories', array('name' => $catname, 'parent' => $parent));
    if (!$category) {

        $data = new stdClass();
        $data->parent = $parent;
        $data->name = $catname;

        $category = core_course_category::create($data);
    }
    return $category->id;
}

function check_lesson($Lesson)
{
    global $DB, $CFG;

    $data = new stdClass();
    $data->shortname = $Lesson->LmsLessonName . "-" . $Lesson->IdNumber;
    $data->fullname = $Lesson->LmsLessonName;
    $data->idnumber = $Lesson->IdNumber;
    $data->category = $Lesson->Categoryid;
    //$data->format = 'weeks';
    //$data->numsections = 10;
    if (time() > $Lesson->LMSendDate) {
        $data->visible = 0;
    } else {
        $data->visible = 1;
    }
    $data->startdate = $Lesson->LMSStartDate;
    $data->numsections = 10;
    $data->showgrades = 1;
    $data->newsitems = 5;
    $data->summary_editor = array(
        'text' => '',
        'format' => 1
    );

    try{

        $params = [$data->idnumber,$data->shortname];
        $sql = "idnumber = '".$data->idnumber ."' OR shortname = '". $data->shortname ."' ";

        $course = $DB->get_record_select('course', $sql);

        if (!$course) {
            create_course($data);
        } else {

            $data->id = $course->id;
            update_course($data);
        }
    }catch (Exception $e){
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
        echo "<pre>";
        var_dump($course);
        echo "</pre>";
        echo $sql . "<br>";
        echo $e->getMessage() . "<br>";
        echo $e->getTraceAsString();
    }


    return $data->shortname;
}

?>
