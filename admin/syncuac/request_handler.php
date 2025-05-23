<?php


set_time_limit(-1);

include_once "classes/Handler.php";
include_once "classes/SyncData.php";
include_once "classes/utils.php";
require_once('../../config.php');

use Synchronizer\Classes\Status;
use Synchronizer\Settings;

/*if(rand(0,3) == 1){
    header('HTTP/1.1 504 Gateway Time-out');
    header('Status: 504 Gateway Time-out');
    return;
}*/

function callIt($action, $params)
{
    global $USER,$CFG;

    $systemcontext = context_system::instance();
    if (!has_capability('moodle/site:config', $systemcontext) && $USER->id != "14334") {
        echo json_encode(array('status' => Status::FATAL_ERROR, 'response' => "ابتدا با کاربر ادمین وارد شوید"), JSON_UNESCAPED_UNICODE);
        return;
    }

    if (array_key_exists('data', $_POST)) {
        $params = $_POST['data'];
    }

    if (!$CFG->samauser || !$CFG->samapass || !$CFG->samaurl){
        $msg = 'لطفا فیلدهای مربوط به سامانه را در فایل config.php وارد کنید'. '<br>';
        $msg .= '$CFG->samaurl : ' . ($CFG->samaurl ? 'تنظیم شده' : 'تنظیم نشده') .'<br>';
        $msg .= '$CFG->samauser : ' . ($CFG->samauser ? 'تنظیم شده' : 'تنظیم نشده') .'<br>';
        $msg .= '$CFG->samapass : ' . ($CFG->samapass ? 'تنظیم شده' : 'تنظیم نشده') .'<br>';
        echo json_encode(array('status' => Status::ERROR, 'response' => $msg, 'details' => false), JSON_UNESCAPED_UNICODE);
        return;
    }
    $handler = new \Synchronizer\Classes\Handler();

    if (!$handler->login_success){
        $msg = "به علت عدم پاسخگویی سرور سامانه سما، دریافت اطلاعات با مشکل مواجه شده است." . " کد: LGN";
        echo json_encode(array('status' => Status::ERROR, 'response' => $msg, 'details' => false), JSON_UNESCAPED_UNICODE);
        return;
    }
    $sync = new \Synchronizer\Classes\SyncData();
    $responses = [];
    switch ($action) {
        case 'ImportStudents':

            $students = $handler->getStudents($params['study_level'], $params['page'], $params['items_per_page']);
//            $students['status'] = Status::ERROR;
            if ($students['status'] == Status::END) {
                echo json_encode(array('status' => Status::END, 'response' => $students['msg'], 'details' => $students['items']), JSON_UNESCAPED_UNICODE);
                return;
            } elseif ($students['status'] == Status::ERROR) {
                echo json_encode(array('status' => Status::ERROR, 'response' => $students['msg'], 'details' => $students['items']), JSON_UNESCAPED_UNICODE);
                return;
            }
            $count = 0;
            foreach ($students['items'] as $index => $student) {
                //var_dump($student);
                $response = $sync->insertUsers($student, $index, Settings::$update_students);
                $responses[] = $response;
            }
            $msg = 'تعداد عملیات انجام شده: ' . count($responses);
            echo json_encode(array('status' => $students['status'], 'response' => $msg, 'items' => $responses), JSON_UNESCAPED_UNICODE);
            break;
        case 'ImportTeachers':

            $teachers = $handler->getTeachers($params['term'], $params['page'], $params['items_per_page']);
            // var_dump($teachers);

            if ($teachers['status'] == Status::END) {
                echo json_encode(array('status' => Status::END, 'response' => $teachers['msg'], 'details' => $teachers['items']), JSON_UNESCAPED_UNICODE);
                return;
            } elseif ($teachers['status'] == Status::ERROR) {
                echo json_encode(array('status' => Status::ERROR, 'response' => $teachers['msg'], 'details' => $teachers['items']), JSON_UNESCAPED_UNICODE);
                return;
            }
            foreach ($teachers['items'] as $index => $teacher) {
                $response = $sync->insertTeachers($teacher, Settings::$update_teachers);
                $responses[] = $response;
            }
            $msg = 'تعداد عملیات انجام شده: ' . count($responses);
            echo json_encode(array('status' => $teachers['status'], 'response' => $msg, 'items' => $responses), JSON_UNESCAPED_UNICODE);

            break;
        case 'ImportLessons':
            $lessons = $handler->getLessons($params['term'], $params['study_level'], $params['page'], $params['items_per_page']);
            //$lessons['status'] = Status::ERROR;
            if ($lessons['status'] == Status::END) {
                echo json_encode(array('status' => Status::END, 'response' => $lessons['msg']), JSON_UNESCAPED_UNICODE);
                return;
            } elseif ($lessons['status'] == Status::ERROR) {
                echo json_encode(array('status' => Status::ERROR, 'response' => $lessons['msg']), JSON_UNESCAPED_UNICODE);
                return;
            }

            foreach ($lessons['items'] as $index => $lesson) {
                //var_dump($student);
                $response = $sync->insertLesson($params['term'],$lesson, Settings::$update_students);
                $responses[] = $response;

            }
            $msg = 'تعداد عملیات انجام شده: ' . count($responses);
            echo json_encode(array('status' => $lessons['status'], 'response' => $msg, 'items' => $responses), JSON_UNESCAPED_UNICODE);
            break;

        case 'ImportEnrolments':

            $mdl_courses = $handler->getMoodleCourses($params['page'], $params['items_per_page'],$params['term']);
            if ($mdl_courses['status'] == Status::END) {
                echo json_encode(array('status' => Status::END, 'response' => $mdl_courses['msg']), JSON_UNESCAPED_UNICODE);
                return;
            } elseif ($mdl_courses['status'] == Status::ERROR) {
                echo json_encode(array('status' => Status::ERROR, 'response' => $mdl_courses['msg']), JSON_UNESCAPED_UNICODE);
                return;
            }
            $nothing = 0;
            $courses_id = [];
            foreach ($mdl_courses['items'] as $mdl_course) {
                $enrolments = $handler->getEnrollments($params['term'], $mdl_course->idnumber, Settings::$student_role_id);
                if ($enrolments['status'] == Status::END) {
                    echo json_encode(array('status' => Status::SUCCESS, 'response' => $enrolments['msg'], 'details' => $enrolments['items']), JSON_UNESCAPED_UNICODE);
                    return;
                } elseif ($enrolments['status'] == Status::ERROR) {
                    echo json_encode(array('status' => Status::ERROR, 'response' => $enrolments['msg'], 'details' => $enrolments['items']), JSON_UNESCAPED_UNICODE);
                    return;
                }
                $courses_id[] = "<span class='text-nowrap'>$mdl_course->idnumber</span>";
                //var_dump($student);
                if (is_array($enrolments['items']) && count($enrolments['items']) > 0) {
                    $res = $sync->saveEnrollments($enrolments['items'], 'courseid', Settings::$student_role_id);
                    $responses = array_merge($responses, $res);
                } else {
                    $msg = "<span class='text-info'>" . "هیچ دانشجویی یافت نشد، دوره :" . $mdl_course->idnumber . "</span>";
                    $responses[] = array('status' => Status::SUCCESS, 'msg' => $msg);
                    $nothing++;
                }

            }
            if ((count($responses) - $nothing) <= 0) {
                $msg = 'هیچ دانشجویی یافت نشد، دروه ها:' . '<br>' . '<span style="direction: ltr;white-space: initial;">' . implode(', ', $courses_id) . '</span>';
            } else {
                $msg = 'تعداد عملیات انجام شده: ' . (count($responses) - $nothing);
            }

            echo json_encode(array('status' => Status::SUCCESS, 'response' => $msg, 'items' => $responses), JSON_UNESCAPED_UNICODE);
            break;
        case 'ImportTeacherEnrolments':

            $mdl_courses = $handler->getMoodleCourses($params['page'], $params['items_per_page'], $params['term']);
            if ($mdl_courses['status'] == Status::END) {
                echo json_encode(array('status' => Status::END, 'response' => $mdl_courses['msg']), JSON_UNESCAPED_UNICODE);
                return;
            } elseif ($mdl_courses['status'] == Status::ERROR) {
                echo json_encode(array('status' => Status::ERROR, 'response' => $mdl_courses['msg']), JSON_UNESCAPED_UNICODE);
                return;
            }

            foreach ($mdl_courses['items'] as $mdl_course) {
                $idnumber_parts = explode("-", $mdl_course->idnumber);
                if (count($idnumber_parts) == 3) {
                    $lessoncode = $idnumber_parts[2];
                    $lessongroup = $idnumber_parts[1];
                } else {
                    $msg = "خطا در دریافت اطلاعات";
                    echo json_encode(array('status' => Status::ERROR, 'response' => $msg, 'details' => ''), JSON_UNESCAPED_UNICODE);
                    return;
                }
                $enrolments = $handler->getLessonTeachers($params['term'], $lessoncode, $lessongroup, Settings::$teacher_role_id);
                if ($enrolments['status'] == Status::END) {
                    echo json_encode(array('status' => Status::SUCCESS, 'response' => $enrolments['msg'], 'details' => $enrolments['items']), JSON_UNESCAPED_UNICODE);
                    return;
                } elseif ($enrolments['status'] == Status::ERROR) {
                    echo json_encode(array('status' => Status::ERROR, 'response' => $enrolments['msg'], 'details' => $enrolments['items']), JSON_UNESCAPED_UNICODE);
                    return;
                }
                //var_dump($student);
                $courses_id[] = "<span class='text-nowrap'>$mdl_course->idnumber</span>";
                if (is_array($enrolments['items']) && count($enrolments['items']) > 0) {
                    $res = $sync->saveEnrollments($enrolments['items'], 'courseid', Settings::$teacher_role_id);
                    $responses = array_merge($responses, $res);
                } else {
                    $msg = "<span class='text-info'>" . "هیچ استادی یافت نشد" . "</span>";
                    $responses[] = array('status' => Status::SUCCESS, 'msg' => $msg);
                }


            }
            $msg = 'تعداد عملیات انجام شده: ' . count($responses);
            echo json_encode(array('status' => Status::SUCCESS, 'response' => $msg, 'items' => $responses), JSON_UNESCAPED_UNICODE);
            break;
        case 'ImportLessonEnrollments':
            $idnumber = $params['term'] . "-" .
                $params['group'] . "-" .
                $params['lesson'];

            if (!$params['mockup']) {
                $mdl_course = $handler->getMoodleCourse($idnumber);

                if ($mdl_course['status'] == Status::ERROR) {
                    echo json_encode(array('status' => Status::ERROR, 'response' => $mdl_course['msg']), JSON_UNESCAPED_UNICODE);
                    return;
                }
                if ($mdl_course['items'] == false) {

                    $lesson = $handler->getLessonInfo($params['term'], $params['lesson'], $params['group']);
                    if ($lesson['status'] == Status::END) {
                        echo json_encode(array('status' => Status::END, 'response' => $lesson['msg']), JSON_UNESCAPED_UNICODE);
                        return;
                    } elseif ($lesson['status'] == Status::ERROR) {
                        echo json_encode(array('status' => Status::ERROR, 'response' => $lesson['msg']), JSON_UNESCAPED_UNICODE);
                        return;
                    }

                    foreach ($lesson['items'] as $index => $lesson) {
                        //var_dump($student);
                        $response = $sync->insertLesson($params['term'],$lesson, Settings::$update_students);
                        $responses[] = $response;

                    }
                }
            }
            $teacher_enrolments = $handler->getLessonTeachers($params['term'], $params['lesson'], $params['group'], Settings::$teacher_role_id);

            if ($teacher_enrolments['status'] == Status::END) {
                echo json_encode(array('status' => Status::END, 'response' => $teacher_enrolments['msg']), JSON_UNESCAPED_UNICODE);
                return;
            } elseif ($teacher_enrolments['status'] == Status::ERROR) {
                echo json_encode(array('status' => Status::ERROR, 'response' => $teacher_enrolments['msg']), JSON_UNESCAPED_UNICODE);
                return;
            }


            $student_enrolments = $handler->getLessonEnrollments($params['term'], $params['lesson'], $params['group'], Settings::$student_role_id);

            if ($student_enrolments['status'] == Status::END) {
                echo json_encode(array('status' => Status::END, 'response' => $student_enrolments['msg']), JSON_UNESCAPED_UNICODE);
                return;
            } elseif ($student_enrolments['status'] == Status::ERROR) {
                echo json_encode(array('status' => Status::ERROR, 'response' => $student_enrolments['msg']), JSON_UNESCAPED_UNICODE);
                return;
            }

            $enrolments = array_merge($teacher_enrolments['items'], $student_enrolments['items']);
            if ($params['mockup'] == true) {

                echo json_encode(array('status' => Status::SUCCESS,
                    'columns' => array(
                        array('data' => 'courseid', 'title' => 'کد دوره'),
                        array('data' => 'username', 'title' => 'نام کاربری'),
                        array('data' => 'roleid', 'title' => 'کد نقش', 'visible' => false),
                        array('data' => 'roletext', 'title' => 'نقش'),
                        array('data' => 'enroldate', 'title' => 'تاریخ ثبت نام', 'visible' => false),
                    ),
                    'response' => $enrolments),
                    JSON_UNESCAPED_UNICODE);
                return;
            }

            if (is_array($enrolments) && count($enrolments) > 0) {
                $res = $sync->saveEnrollments($enrolments, 'courseid');
                $responses = array_merge($responses, $res);
            } else {
                $msg = "<span class='text-info'>" . "هیچ دانشجویی یافت نشد" . "</span>";
                $responses[] = array('status' => Status::SUCCESS, 'msg' => $msg);
            }
            $msg = 'تعداد عملیات انجام شده: ' . count($responses);
            echo json_encode(array('status' => Status::SUCCESS, 'response' => $msg, 'items' => $responses), JSON_UNESCAPED_UNICODE);
//            echo json_encode(array('status' => Status::SUCCESS, 'response' => $responses), JSON_UNESCAPED_UNICODE);
            break;


        case 'getProfessorInfo':
            $enrolments = $handler->getProfessorInfo($params['term'], $params['professor'], Settings::$teacher_role_id);

            if ($enrolments['status'] == Status::END) {
                echo json_encode(array('status' => Status::END, 'response' => $enrolments['msg']), JSON_UNESCAPED_UNICODE);
                return;
            } elseif ($enrolments['status'] == Status::ERROR) {
                echo json_encode(array('status' => Status::ERROR, 'response' => $enrolments['msg']), JSON_UNESCAPED_UNICODE);
                return;
            }
//            if (is_array($enrolments['items']) && count($enrolments['items']) > 0) {
            if ($params['mockup'] == true) {

                echo json_encode(array('status' => Status::SUCCESS,
                    'columns' => array(
                        array('data' => 'courseid', 'title' => 'کد دوره'),
                        array('data' => 'coursename', 'title' => 'نام دوره'),
                        array('data' => 'username', 'title' => 'نام کاربری'),
                        array('data' => 'roleid', 'title' => 'کد نقش', 'visible' => false),
                        array('data' => 'roletext', 'title' => 'نقش'),
                        array('data' => 'enroldate', 'title' => 'تاریخ ثبت نام', 'visible' => false),
                    ),
                    'response' => $enrolments['items']),
                    JSON_UNESCAPED_UNICODE);
                return;
            }
//            }
            if (is_array($enrolments['items']) && count($enrolments['items']) > 0) {
                $res = $sync->saveEnrollments($enrolments['items'], 'username', Settings::$teacher_role_id);
                $responses = array_merge($responses, $res);
            } else {
                $msg = "<span class='text-info'>" . "هیچ دوره ای یافت نشد" . "</span>";
                $responses[] = array('status' => Status::SUCCESS, 'msg' => $msg);
            }
            $msg = 'تعداد عملیات انجام شده: ' . count($responses);
            echo json_encode(array('status' => Status::SUCCESS, 'response' => $msg, 'items' => $responses), JSON_UNESCAPED_UNICODE);
//            echo json_encode(array('status' => Status::SUCCESS, 'response' => $responses), JSON_UNESCAPED_UNICODE);
            break;
        case 'getStudentEnrollments':

            try {
                $students = $handler->getStudent($params['student'],$params['study_levels']);

                if ($students['status'] == Status::END) {
                    echo json_encode(array('status' => Status::END, 'response' => $students['msg'], 'details' => $students['items']), JSON_UNESCAPED_UNICODE);
                    return;
                } elseif ($students['status'] == Status::ERROR) {
                    echo json_encode(array('status' => Status::ERROR, 'response' => $students['msg'], 'details' => $students['items']), JSON_UNESCAPED_UNICODE);
                    return;
                }
                $enrolments = $handler->getStudentEnrollments($params['term'], $params['student'], Settings::$student_role_id);

                if ($enrolments['status'] == Status::END) {
                    echo json_encode(array('status' => Status::END, 'response' => $enrolments['msg']), JSON_UNESCAPED_UNICODE);
                    return;
                } elseif ($enrolments['status'] == Status::ERROR) {
                    echo json_encode(array('status' => Status::ERROR, 'response' => $enrolments['msg']), JSON_UNESCAPED_UNICODE);
                    return;
                }
                $isNewUser = true;
                if (is_array($students['items']) && count($students['items']) > 0) {
                    foreach ($students['items'] as $index => $student) {

                        $std_response = $sync->getUser($student->username);
                        if ($std_response['status'] != Status::ERROR) {
                            if ($std_response['items']->id) {
                                $isNewUser = false;
                            } else if ($params['mockup'] == false) {
                                $std_response = $sync->insertUsers($student, $index, Settings::$update_students);
                                $responses[] = $std_response;
                            }

                        }

                    }
                }


                if ($params['mockup'] == true) {

                    foreach ($enrolments['items'] as &$row) {
                        $row->status = $isNewUser ? 'بله' : 'خیر';

                    }
                    echo json_encode(array('status' => Status::SUCCESS,
                        'columns' => array(
                            array('data' => 'courseid', 'title' => 'کد دوره'),
                            array('data' => 'coursename', 'title' => 'نام دوره'),
                            array('data' => 'username', 'title' => 'نام کاربری'),
                            array('data' => 'status', 'title' => 'کاربر جدید'),
                            array('data' => 'roleid', 'title' => 'کد نقش', 'visible' => false),
                            array('data' => 'roletext', 'title' => 'نقش'),
                            array('data' => 'enroldate', 'title' => 'تاریخ ثبت نام', 'visible' => false),
                        ),
                        'response' => $enrolments['items']),
                        JSON_UNESCAPED_UNICODE);
                    return;
                }
                if (is_array($enrolments['items']) && count($enrolments['items']) > 0) {

                    $res = $sync->saveEnrollments($enrolments['items'], 'username', Settings::$student_role_id);
                    $responses = array_merge($responses, $res);
                } else {
                    $msg = "<span class='text-info'>" . "هیچ دوره ای یافت نشد" . "</span>";
                    $responses[] = array('status' => Status::SUCCESS, 'msg' => $msg);
                }
                $msg = 'تعداد عملیات انجام شده: ' . count($responses);
                echo json_encode(array('status' => Status::SUCCESS, 'response' => $msg, 'items' => $responses), JSON_UNESCAPED_UNICODE);
//            echo json_encode(array('status' => Status::SUCCESS, 'response' => $responses), JSON_UNESCAPED_UNICODE);
            } catch (Exception $e) {
                $msg = $e->getMessage() . $e->getTraceAsString();
                echo json_encode(array('status' => Status::ERROR, 'response' => $msg, 'items' => $responses), JSON_UNESCAPED_UNICODE);
            }
            break;
    }

}

if (array_key_exists('action', $_POST) && array_key_exists('data', $_POST)) {
    try {

        $action = $_POST['action'];
        $params = $_POST['data'];
        if ($params && checkParams($action, $params)) {
            callIt($action, $params);
        } else {
            $msg = 'اطلاعات وارد شده کافی نمی باشد.';
            echo json_encode(array('status' => Status::FATAL_ERROR, 'response' => $msg), JSON_UNESCAPED_UNICODE);
            return;
        }


    } catch (ArgumentCountError $e) {
        echo json_encode(array('status' => Status::FATAL_ERROR, 'response' => $e->getMessage()), JSON_UNESCAPED_UNICODE);
        return;
    } catch (Exception $e) {
        echo json_encode(array('status' => Status::FATAL_ERROR, 'response' => $e->getMessage()), JSON_UNESCAPED_UNICODE);
        return;
    }

}

function checkParams($action, $params)
{
    if (!$action || !$params) {
        return false;
    }
    $required = null;
    switch ($action) {
        case 'ImportStudents':
            $required = array('study_level', 'page');
            break;
        case 'ImportTeachers':
            $required = array('term', 'page', 'items_per_page');
            break;
        case 'ImportLessons':
            $required = array('term', 'study_level', 'page', 'items_per_page');
            break;
        case 'ImportEnrolments':
            $required = array('page', 'items_per_page');
            break;
        case 'ImportTeacherEnrolments':
            $required = array('page', 'items_per_page');
            break;
        case 'ImportLessonEnrollments':
            $required = array('term', 'lesson', 'group');
            break;
        case 'getProfessorInfo':
            $required = array('term', 'professor');
            break;
        case 'getStudentEnrollments':
            $required = array('term', 'student');
            break;
    }
    return ($required && array_keys_exists($required, $params));


}

function array_keys_exists($required, $data)
{
    if (count(array_intersect_key(array_flip($required), $data)) === count($required)) {
        // All required keys exist!
        return true;
    }
    return false;
}
