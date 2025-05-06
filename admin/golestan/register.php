<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 10/28/2020
 * Time: 3:37 PM
 */
require_once "locallib.php";

if (isset($_POST['key']) && $_POST['key'] == Config::$security_key)
    if (isset($_POST['action']) && $_POST['action'] && isset($_POST['data']) && $_POST['data']) {
        $db = new SyncDB();

        $action = $_POST['action'];
        $obj = $_POST['data'];
        $params = $_POST['params'] ?: null;

        //sleep(1);
        if ($action == 'enroll') {
            $msg = $db->syncEnrolment($obj);
            echo json_encode(array('success' => 1, 'msg' => $msg));
        } elseif ($action == 'students') {
            $msg = $db->insertUsers($obj, Config::$update_students);
            echo json_encode(array('success' => 1, 'msg' => $msg));
        } elseif ($action == 'teachers') {
            $msg = $db->insertUsers($obj, Config::$update_teachers);
            echo json_encode(array('success' => 1, 'msg' => $msg));
        } elseif ($action == 'courses') {
            $msg = $db->syncCourses($obj, Config::$update_courses,$params);
            echo json_encode(array('success' => 1, 'msg' => $msg));
        } elseif ($action == 'unenroll') {
            $msg = $db->unEnroll($obj,$params);
            echo json_encode(array('success' => 1, 'msg' => $msg));
        } else {
            echo json_encode(array('success' => 0,'msg' => 'اطلاعات وارد شده اشتباه می باشد'));
        }


    } else {
        echo json_encode(array('success' => 0,'msg' => 'اطلاعات وارد شده ناقص می باشد'));
    }
    else
        echo json_encode(array('success' => 0,'msg' => "کد امنیتی نامعتبر"));
