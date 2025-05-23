<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 2/17/2021
 * Time: 2:23 PM
 */

namespace Synchronizer\Classes;

require_once "Student.php";
require_once "Teacher.php";
require_once "Course.php";
require_once "Category.php";
include_once "Enrollment.php";
include_once "Cookie.php";
include_once "settings.php";
require_once('../../config.php');

use Synchronizer\Settings;

/** @noinspection PhpUndefinedVariableInspection */
require_once $CFG->dirroot.'/user/lib.php';

class Handler
{
    private $auth;
    private $asp_auth;
    public $login_success;

    public function __construct()
    {
        $this->login_success = $this->login();
    }

    public function getStudents($study_level, $page, $items_per_page = 100)
    {
        global $CFG;
        //        $auth = $this->login();
        //$page = $page == 1 ? 500 : 1000;
        $data = array(
            'pageNumber'   => $page,
            'pageRows'     => $items_per_page,
            //'studyLevelId' => $study_level
        );
        $params = http_build_query($data, null, '&');

        // API URL
        $url = $CFG->samaurl.'/services/StudentService.svc/web/2019/01/GetStudentsPersonInfo?'.$params;

        // Create a new cURL resource
        $ch = curl_init($url);

        // Return response instead of outputting
        $cookies = [
            "Cookie: ".$this->auth->name."=".$this->auth->value,
            "Cookie: ".$this->asp_auth->name."=".$this->asp_auth->value
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cookies);

        // Execute the POST request
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        // Close cURL resource
        curl_close($ch);

        if ($result == '"این لیست موجود نیست"') {
            $msg = "این لیست موجود نمی باشد.";
            return array('status' => Status::END, 'msg' => $msg);

        }
        $students = json_decode($result);
        // $user = new Student("asd","asd","student7","asd","asd");
        $statusId = array(1, 3, 9, 14, 18, 24, 31, 39, 43, 50, 51, 58, 60, 62);
        $users = array();
        if (is_object($students) || is_array($students)) {
            foreach ($students as $student) {
                if (in_array($student->StudentStatus->StudentStatusId, $statusId)) {
                    if ($student->Person->FullName) {
                        $firstName = $student->Person->FirstName;
                        $lastName = $student->Person->LastName;
                        if (strlen(trim($lastName)) == 0 || strlen(trim($firstName)) == 0) {
                            $name = explode("-", $student->Person->FullName);
                            if (count($name) > 1) {
                                $firstName = trim($name[1]);
                                $lastName = trim($name[0]);
                            } else {
                                $name = preg_split('/\s/', $student->Person->FullName);
                                if (count($name) > 1) {
                                    $firstName = trim($name[1]);
                                    $lastName = trim($name[0]);
                                } else {
                                    $firstName = "_";
                                    $lastName = $student->Person->FullName;
                                }

                            }
                        }
                        $national_code = $student->Person->NationalCode;
                        $email = $student->Person->Email;
                        $national_code = preg_replace("/[^0-9]/", "", $national_code);
                        $studentNumber = preg_replace("/[^0-9]/", "", $student->StudentNumber);
                        if (strlen(trim($national_code)) == 0) {
                            $national_code = $studentNumber;
                        }

                        if (strlen(trim($email)) == 0 || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $email = $national_code."@smums.ac.ir";
                        }
                        $user = new Student($firstName, $lastName, "s".$studentNumber,
                            $national_code, $student->StudyLevel->StudyLevelIntId, $email);
                        $users[] = $user;
                    }

                }
            }
        } else {
            $msg = "به علت عدم پاسخگویی سرور سامانه سما، دریافت اطلاعات با مشکل مواجه شده است."." کد: S1-NSF";
            logit($url);
            logit($students);
            return array('status' => Status::ERROR, 'msg' => $msg, 'items' => $result);

        }

        $msg = "در حال ثبت اطلاعات دانشپذیران"." : ".count($users);
        //$msg = "Current page = $page \n current url= $url";
        return array(
            'status' => Status::SUCCESS, 'msg' => $msg, 'sec_key' => Settings::$security_key,
            'items'  => $users
        );

    }

    public function getStudent($student_id, $study_levels, $items_per_page = 100)
    {
        global $CFG;
        //        $auth = $this->login();
        //$page = $page == 1 ? 500 : 1000;
        $data = array(
            'StudentNumber' => $student_id
        );
        $params = http_build_query($data, null, '&');

        // API URL
        $url = $CFG->samaurl.'/services/StudentService.svc/web/2019/01/GetStudentPersonInfo?'.$params;

        // Create a new cURL resource
        $ch = curl_init($url);

        // Return response instead of outputting
        $cookies = [
            "Cookie: ".$this->auth->name."=".$this->auth->value,
            "Cookie: ".$this->asp_auth->name."=".$this->asp_auth->value
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cookies);
        // Execute the POST request
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        // Close cURL resource
        curl_close($ch);

        if ($result == '"دانشجو با شماره دانشجویی وارد شده موجود نیست"') {
            $msg = "دانشجو با شماره دانشجویی وارد شده موجود نیست";
            return array(
                'status' => Status::END, 'msg' => $msg, 'sec_key' => Settings::$security_key,
                'items'  => []
            );
        }
        $student = json_decode($result);
        // $user = new Student("asd","asd","student7","asd","asd");
        $statusId = array(1, 3, 9, 14, 18, 24, 31, 39, 43, 50, 51, 58, 60, 62);
        //$study_levels = explode(',', $study_levels);
        //if (!$study_levels) {
        //    $study_levels = array(5, 7);
        //}

        $users = array();
        if (is_object($student)) {
            if (in_array($student->StudentStatus->StudentStatusId, $statusId)
                //&& in_array($student->StudyLevel->StudyLevelIntId, $study_levels)
            ) {
                if ($student->Person->FullName) {
                    $firstName = $student->Person->FirstName;
                    $lastName = $student->Person->LastName;
                    if (strlen(trim($lastName)) == 0 || strlen(trim($firstName)) == 0) {
                        $name = explode("-", $student->Person->FullName);
                        if (count($name) > 1) {
                            $firstName = trim($name[1]);
                            $lastName = trim($name[0]);
                        } else {
                            $name = preg_split('/\s/', $student->Person->FullName);
                            if (count($name) > 1) {
                                $firstName = trim($name[1]);
                                $lastName = trim($name[0]);
                            } else {
                                $firstName = "_";
                                $lastName = $student->Person->FullName;
                            }

                        }
                    }
                    $national_code = $student->Person->NationalCode;
                    $email = $student->Person->Email;
                    $national_code = preg_replace("/[^0-9]/", "", $national_code);
                    $studentNumber = $student_id;
                    if (strlen(trim($national_code)) == 0) {
                        $national_code = $studentNumber;
                    }

                    if (strlen(trim($email)) == 0 || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $email = $national_code."@smums.ac.ir";
                    }
                    $user = new Student($firstName, $lastName, "s".$studentNumber,
                        $national_code, $student->StudyLevel->StudyLevelIntId, $email);
                    $users[] = $user;
                }

            } else {
                $msg = "ثبت نام دانشجو به دلیل فیلتر وضعیت دانشجو و یا مقطع تحصیلی امکان پذیر نیست."."<br>".
                    "وضعیت: ".$student->StudentStatus->Title." کد ".$student->StudentStatus->StudentStatusId."<br>".
                    "مقطع تحصیلی: ".$student->StudyLevel->Title." کد ".$student->StudyLevel->StudyLevelId;
                return array(
                    'status' => Status::END, 'msg' => $msg, 'sec_key' => Settings::$security_key,
                    'items'  => []
                );
            }
        } else {
            $msg = "به علت عدم پاسخگویی سرور سامانه سما، دریافت اطلاعات با مشکل مواجه شده است."." کد: S1-NSF-Single";
            logit($url);
            logit($student);
            return array('status' => Status::ERROR, 'msg' => $msg, 'items' => $result);

        }

        $msg = "در حال ثبت اطلاعات دانشپذیران"." : ".count($users);
        //$msg = "Current page = $page \n current url= $url";
        return array(
            'status' => Status::SUCCESS, 'msg' => $msg, 'sec_key' => Settings::$security_key,
            'items'  => $users
        );

    }

    public function getTeachers($term, $page, $items_per_page = 1000)
    {
        global $CFG;

        $data = array(
            'pageNumber' => $page,
            'pageRows'   => $items_per_page
        );
        $params = http_build_query($data, null, "&");

        // API URL
        $url = $CFG->samaurl.'/services/ProfessorService.svc/web/2019/01/GetProfessorList?'.
            $params;

        // Create a new cURL resource
        $ch = curl_init($url);

        // Return response instead of outputting
        $cookies = [
            "Cookie: ".$this->auth->name."=".$this->auth->value,
            "Cookie: ".$this->asp_auth->name."=".$this->asp_auth->value
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cookies);

        // Execute the POST request
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        // Close cURL resource
        curl_close($ch);
        if ($result == '"این لیست موجود نیست"') {
            $msg = "این لیست موجود نمی باشد.";
            return array('status' => Status::END, 'msg' => $msg);

        }
        $teachers = json_decode($result);
        // $user = new Student("asd","asd","student7","asd","asd");

        $users = array();
        // $study_levels = array(5, 6, 7, 8);
        if (is_object($teachers) || is_array($teachers)) {
            foreach ($teachers as $teacher) {
                if (true) {
                    if (true) {
                        if ($teacher->Person->FullName) {
                            $firstName = $teacher->Person->FirstName;
                            $lastName = $teacher->Person->LastName;
                            if (strlen(trim($lastName)) == 0 || strlen(trim($firstName)) == 0) {
                                $name = explode("-", $teacher->Person->FullName);
                                if (count($name) > 1) {
                                    $firstName = trim($name[1]);
                                    $lastName = trim($name[0]);
                                } else {
                                    $name = preg_split('/\s/', $teacher->Person->FullName);
                                    if (count($name) > 1) {
                                        $firstName = trim($name[1]);
                                        $lastName = trim($name[0]);
                                    } else {
                                        $firstName = "_";
                                        $lastName = $teacher->Person->FullName;
                                    }

                                }
                            }
                            $national_code = $teacher->Person->NationalCode;
                            $email = $teacher->Person->Email;
                            $national_code = preg_replace("/[^0-9]/", "", $national_code);
                            $professorCode = preg_replace("/[^0-9]/", "", $teacher->ProfessorCode);
                            if (strlen(trim($national_code)) == 0) {
                                $national_code = $professorCode;
                            }

                            if (strlen(trim($email)) == 0 || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $email = $national_code."@smums.ac.ir";
                            }
                            $user = new Teacher($firstName, $lastName, "t".$professorCode,
                                $national_code, $email);
                            $user->term = $term;
                            $users[] = $user;
                        }
                    }

                }
            }
        } else {
            $msg = "به علت عدم پاسخگویی سرور سامانه سما، دریافت اطلاعات با مشکل مواجه شده است."." کد: T1";
            logit($url);
            logit($teachers);
            return array('status' => Status::ERROR, 'msg' => $msg, 'items' => $result);
        }

        $msg = "در حال ثبت اطلاعات اساتید";

        return array(
            'status' => Status::SUCCESS, 'msg' => $msg, 'sec_key' => Settings::$security_key,
            'items'  => $users
        );
    }

    public function getLessons($term, $studyLevelId, $page, $items_per_page = 1000)
    {
        global $CFG;
        //        $auth = $this->login();

        $data = array(
            'termCode'     => $term,
            'pageRows'     => $items_per_page,
            'pageNumber'   => $page,
            //'studyLevelId' => $studyLevelId
        );
        $params = http_build_query($data, null, "&");

        // API URL
        $url = $CFG->samaurl.'/services/EducationService.svc/web/2019/01/GetTermLessonList?'.$params;

        // Create a new cURL resource
        $ch = curl_init($url);

        // Return response instead of outputting
        $cookies = [
            "Cookie: ".$this->auth->name."=".$this->auth->value,
            "Cookie: ".$this->asp_auth->name."=".$this->asp_auth->value
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cookies);

        // Execute the POST request
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        // Close cURL resource
        curl_close($ch);

        $lessons = json_decode($result);
        // $user = new Student("asd","asd","student7","asd","asd");
        if ($result == '"این لیست موجود نیست"') {
            $msg = "این لیست موجود نمی باشد.";
            return array('status' => Status::END, 'msg' => $msg);

        }
        $courses = array();
        if (is_object($lessons) || is_array($lessons)) {
            foreach ($lessons as $lesson) {

                if (true) {
                    if (true) {
                        if ($lesson->Lesson->LessonCode) {

                            $groupTitle = "گروه درسی ".(int) $lesson->LessonGroup;

                            $idnumber = $term."-".
                                ((int) $lesson->LessonGroup)."-".
                                $lesson->Lesson->LessonCode;
                            $shortname = $idnumber."-".$lesson->Lesson->LessonName;
                            $fullname = $lesson->Lesson->LessonName;
                            $category = new Category(
                                (string) $lesson->FacultyCode,
                                ($lesson->FacultyTitle ?: 'دانشکده ' . $lesson->FacultyCode),
                                (int) $lesson->LessonGroup,
                                $groupTitle
                            );
                            $course = new Course($idnumber, $fullname, $shortname,
                                $category);
                            $courses[] = $course;
                        }
                    }

                }

            }

            $courses = $this->array_unique_objects($courses);
        } else {
            $msg = "به علت عدم پاسخگویی سرور سامانه سما، دریافت اطلاعات با مشکل مواجه شده است."." کد: C0";
            logit($url);
            logit($lessons);
            return array('status' => Status::ERROR, 'msg' => $msg, 'items' => $courses);

        }

        //$courses = array();
        $msg = "در حال ثبت اطلاعات دروس";
        //$msg = "Current page = $page \n current url= $url";
        return array(
            'status' => Status::SUCCESS, 'msg' => $msg, 'sec_key' => Settings::$security_key,
            'items'  => $courses
        );

    }

    public function getMoodleCourses($page, $items_per_page, $term = "%")
    {
        global $DB;
        $start = $items_per_page * ($page - 1);
        try {
            $courses = $DB->get_recordset_select('course', "idnumber LIKE '{$term}-%-%'", null, '', 'idnumber', $start,
                $items_per_page);
            $msg = "در حال ثبت اطلاعات ثبت نام";
            //$msg = "Current page = $page \n current url= $url";
            //var_dump($courses);
            if ($courses->valid()) {
                return array(
                    'status' => Status::SUCCESS, 'msg' => $msg, 'sec_key' => Settings::$security_key,
                    'items'  => $courses
                );
            } else {
                $msg = "این لیست موجود نمی باشد.";
                return array(
                    'status' => Status::END, 'msg' => $msg, 'sec_key' => Settings::$security_key,
                    'items'  => $courses
                );
            }

        } catch (\dml_exception $e) {
            $msg = "به علت عدم پاسخگویی سرور سامانه سما، دریافت اطلاعات با مشکل مواجه شده است."." کد: C1";
            $msg .= "<br>".$e->getMessage();
            return array('status' => Status::ERROR, 'msg' => $msg, 'items' => []);
        }

    }

    public function getMoodleCourse($idnumber)
    {
        global $DB;

        try {
            $course = $DB->get_record('course', array("idnumber" => $idnumber));
            $msg = "در حال ثبت اطلاعات ثبت نام";
            //$msg = "Current page = $page \n current url= $url";
            return array(
                'status' => Status::SUCCESS, 'msg' => $msg, 'sec_key' => Settings::$security_key,
                'items'  => $course
            );
        } catch (\dml_exception $e) {
            $msg = "به علت عدم پاسخگویی سرور سامانه سما، دریافت اطلاعات با مشکل مواجه شده است."." کد: C2";
            $msg .= "<br>".$e->getMessage();
            return array('status' => Status::ERROR, 'msg' => $msg, 'items' => []);
        }

    }

    public function getLessonEnrollments($term, $lesson_id, $group_id, $roleid = null)
    {
        global $CFG;
        //      $auth = $this->login();

        $data = array(
            'termCode'    => $term,
            'LessonCode'  => $lesson_id,
            'LessonGroup' => $group_id,
        );

        $idnumber = $term."-".
            $group_id."-".
            $lesson_id;
        $params = http_build_query($data, null, "&");

        // API URL
        $url = $CFG->samaurl.'/services/EducationService.svc/web/2019/01/GetStudentsOfTermLesson?'.
            $params;

        // Create a new cURL resource
        $ch = curl_init($url);

        // Return response instead of outputting
        $cookies = [
            "Cookie: ".$this->auth->name."=".$this->auth->value,
            "Cookie: ".$this->asp_auth->name."=".$this->asp_auth->value
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cookies);

        // Execute the POST request
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        // Close cURL resource
        curl_close($ch);
        if ($result == '"این لیست موجود نیست"') {
            $msg = "این لیست موجود نمی باشد.";
            return array('status' => Status::END, 'msg' => $msg);
            //            header('Content-Type: application/json');
            //            echo json_encode(array('success' => Status::END, 'msg' => $msg), JSON_UNESCAPED_UNICODE);
            //            return;
        }
        $students = json_decode($result);
        // $user = new Student("asd","asd","student7","asd","asd");

        $enrolments = array();
        if (is_object($students) || is_array($students)) {
            foreach ($students as $student) {

                if (true) {
                    if (true) {
                        if ($student->StudentNumber) {
                            $enrolment = new Enrollment($idnumber,
                                "s".$student->StudentNumber, $roleid,
                                $term, 1);
                            $enrolment->roletext = ($roleid == Settings::$teacher_role_id) ? "استاد" : "دانشپذیر";
                            $enrolments[] = $enrolment;
                        }
                    }

                }

            }
        } else {
            $msg = "به علت عدم پاسخگویی سرور سامانه سما، دریافت اطلاعات با مشکل مواجه شده است."." کد: LE1";
            logit($url);
            logit($students);
            return array('status' => Status::ERROR, 'msg' => $msg, 'items' => $enrolments);

        }

        //$courses = array();
        $msg = "در حال ثبت نام دانشپذیران";
        return array(
            'status' => Status::SUCCESS, 'msg' => $msg, 'sec_key' => Settings::$security_key,
            'items'  => $enrolments
        );

    }

    public function getLessonTeachers($term, $lesson_id, $group_id, $roleid = null)
    {
        global $CFG;
        //$auth = $this->login();

        $idnumber = $term."-".
            $group_id."-".
            $lesson_id;
        $data = array(
            'termCode'    => $term,
            'LessonCode'  => $lesson_id,
            'LessonGroup' => $group_id,
        );

        $params = http_build_query($data, null, "&");

        // API URL
        $url = $CFG->samaurl.'/services/EducationService.svc/web/2019/01/GetTermLessonScheduleList?'.$params;

        // Create a new cURL resource
        $ch = curl_init($url);

        // Return response instead of outputting
        $cookies = [
            "Cookie: ".$this->auth->name."=".$this->auth->value,
            "Cookie: ".$this->asp_auth->name."=".$this->asp_auth->value
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cookies);

        // Execute the POST request
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        // var_dump($url);
        // Close cURL resource
        curl_close($ch);
        if ($result == '"این لیست موجود نیست"') {
            $msg = "این لیست موجود نمی باشد." . "({$idnumber})";
            return array('status' => Status::END, 'msg' => $msg);
        }
        $teachers = json_decode($result);
        // $user = new Student("asd","asd","student7","asd","asd");

        $enrolments = [];
        if (is_object($teachers) || is_array($teachers)) {
            foreach ($teachers as $teacher) {

                $pf = $teacher->ProfessorLesson->ProfessorInfo;
                if ($pf) {
                    if ($pf->ProfessorCode) {
                        $enrolment = new Enrollment($idnumber,
                            "t".$pf->ProfessorCode, $roleid,
                            $term, 1);
                        $enrolment->roletext = ($roleid == Settings::$teacher_role_id) ? "استاد" : "دانشپذیر";
                        $enrolments[] = $enrolment;
                    }
                }

            }
        } else {
            $msg = "به علت عدم پاسخگویی سرور سامانه سما، دریافت اطلاعات با مشکل مواجه شده است."." کد: LTE1";
            logit($url);
            logit($teachers);
            return array('status' => Status::ERROR, 'msg' => $msg, 'items' => $result);

        }
        //$courses = array();
        $msg = "در حال ثبت نام اساتید";
        return array(
            'status' => Status::SUCCESS, 'msg' => $msg, 'sec_key' => Settings::$security_key,
            'items'  => $enrolments
        );

    }

    public function getStudentEnrollments($term, $studentNumber, $roleid = null)
    {
        global $CFG;
        //        $auth = $this->login();

        $data = array(
            'termCode'           => $term,
            'studentNumber'      => $studentNumber,
            //'lessonSalaryStatus' => 1,
        );

        $params = http_build_query($data, null, "&");

        // API URL
        $url = $CFG->samaurl.'/services/StudentService.svc/web/2019/01/GetStudentLessonList?'.$params;

        // Create a new cURL resource
        $ch = curl_init($url);

        // Return response instead of outputting
        $cookies = [
            "Cookie: ".$this->auth->name."=".$this->auth->value,
            "Cookie: ".$this->asp_auth->name."=".$this->asp_auth->value
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cookies);

        // Execute the POST request
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        // Close cURL resource
        curl_close($ch);

        $lessons = json_decode($result);
        if ($result == '"این لیست موجود نیست"') {
            $msg = "هیچ دوره ای برای این دانشجو یافت نشد.";
            return array('status' => Status::END, 'msg' => $msg);
        }
        $enrolments = array();
        if (is_object($lessons) || is_array($lessons)) {
            foreach ($lessons as $lesson) {
                //if ($lesson->StudentLessonStatus->StudentLessonStatusId == 2) {
                //    continue;
                //}
                $idnumber = $term."-".
                    ((int) $lesson->TermLesson->LessonGroup)."-".
                    $lesson->TermLesson->Lesson->LessonCode;
                $enrolment = new Enrollment($idnumber,
                    "s".$studentNumber, $roleid,
                    $term, 1);
                $enrolment->roletext = ($roleid == Settings::$teacher_role_id) ? "استاد" : "دانشپذیر";
                $enrolment->coursename = $lesson->TermLesson->Lesson->LessonName;
                $enrolments[] = $enrolment;

            }
        } else {
            $msg = "به علت عدم پاسخگویی سرور سامانه سما، دریافت اطلاعات با مشکل مواجه شده است."." کد: SE1";
            logit($url);
            logit($lessons);
            return array('success' => Status::ERROR, 'msg' => $msg, 'url' => $url, 'items' => $enrolments);

        }

        $msg = "در حال ثبت نام دانشپذیران";
        return array(
            'success' => Status::SUCCESS, 'msg' => $msg, 'sec_key' => Settings::$security_key,
            'items'   => $enrolments
        );

    }

    public function getEnrollments($term, $course_idnumber, $roleid = null)
    {
        global $CFG;
        //        $auth = $this->login();

        $idnumber_parts = explode("-", $course_idnumber);
        if (count($idnumber_parts) == 3) {
            $lessoncode = $idnumber_parts[2];
            $lessongroup = $idnumber_parts[1];
        } else {
            $msg = "خطا در دریافت اطلاعات";
            return array('status' => Status::ERROR, 'msg' => $msg);
        }

        $data = array(
            'termCode'    => $term,
            'LessonCode'  => ($lessoncode),
            'LessonGroup' => $lessongroup,
        );
        $params = http_build_query($data, null, "&");

        // API URL
        $url = $CFG->samaurl.'/services/EducationService.svc/web/2019/01/GetStudentsOfTermLesson?'.
            $params;

        // Create a new cURL resource
        $ch = curl_init($url);

        // Return response instead of outputting
        $cookies = [
            "Cookie: ".$this->auth->name."=".$this->auth->value,
            "Cookie: ".$this->asp_auth->name."=".$this->asp_auth->value
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cookies);

        // Execute the POST request
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        // Close cURL resource
        curl_close($ch);
        if ($result == '"این لیست موجود نیست"') {
            $msg = "این لیست موجود نمی باشد.";
            return array('status' => Status::END, 'msg' => $msg);

        }
        $students = json_decode($result);
        // $user = new Student("asd","asd","student7","asd","asd");

        $enrolments = array();
        if (is_object($students) || is_array($students)) {
            foreach ($students as $student) {

                if (true) {
                    if (true) {
                        if ($student->StudentNumber) {
                            $enrolment = new Enrollment($course_idnumber,
                                "s".$student->StudentNumber, $roleid,
                                $term, 1);
                            $enrolment->roletext = ($roleid == Settings::$teacher_role_id) ? "استاد" : "دانشپذیر";
                            $enrolments[] = $enrolment;
                        }
                    }

                }

            }
        } else {
            $msg = "به علت عدم پاسخگویی سرور سامانه سما، دریافت اطلاعات با مشکل مواجه شده است."." کد: E1";
            logit($url);
            logit($students);
            return array('status' => Status::ERROR, 'msg' => $msg, 'items' => $result);

        }

        $msg = "در حال ثبت نام دانشپذیران";
        return array(
            'status' => Status::SUCCESS, 'msg' => $msg, 'sec_key' => Settings::$security_key,
            'items'  => $enrolments
        );

    }

    public function getProfessorInfo($term, $teacher_code, $roleid = null)
    {
        global $CFG;

        if (!$roleid) {
            $roleid = Settings::$teacher_role_id;
        }

        $data = array(
            'termCode'      => $term,
            'ProfessorCode' => $teacher_code
        );
        $params = http_build_query($data, null, "&");

        // API URL
        $url = $CFG->samaurl.'/services/ProfessorService.svc/web/2019/01/GetProfessorLessonList?'.$params;

        // Create a new cURL resource
        $ch = curl_init($url);

        // Return response instead of outputting
        $cookies = [
            "Cookie: ".$this->auth->name."=".$this->auth->value,
            "Cookie: ".$this->asp_auth->name."=".$this->asp_auth->value
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cookies);

        // Execute the POST request
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        // Close cURL resource
        curl_close($ch);
        if ($result == '"این لیست موجود نیست"') {
            $msg = "این لیست موجود نمی باشد.";
            return array('status' => Status::END, 'msg' => $msg);
        }
        $lessons = json_decode($result);
        // $user = new Student("asd","asd","student7","asd","asd");

        $enrolments = array();
        // $study_levels = array(5, 6, 7, 8);
        if (is_object($lessons) || is_array($lessons)) {

            foreach ($lessons as $lesson) {
                if (true) {
                    if (true) {
                        if ($lesson->TermLesson) {
                            $idnumber = $term."-".
                                ((int) $lesson->TermLesson->LessonGroup)."-".
                                $lesson->TermLesson->Lesson->LessonCode;

                            $enrolment = new Enrollment($idnumber, "t".$teacher_code, $roleid,
                                $term, 1);
                            $enrolment->roletext = ($roleid == Settings::$teacher_role_id) ? "استاد" : "دانشپذیر";
                            $enrolment->coursename = $lesson->TermLesson->Lesson->LessonName;
                            $enrolments[] = $enrolment;

                        }
                    }

                }
            }
        } else {
            $msg = "به علت عدم پاسخگویی سرور سامانه سما، دریافت اطلاعات با مشکل مواجه شده است."." کد: TI1";
            logit($url);
            logit($lessons);
            return array('status' => Status::ERROR, 'msg' => $msg, 'items' => $enrolments);

        }

        //$courses = array();
        $msg = "در حال ثبت نام اساتید";
        return array(
            'status' => Status::SUCCESS, 'msg' => $msg, 'sec_key' => Settings::$security_key,
            'items'  => $enrolments
        );

    }

    public function getLessonInfo($term, $lesson, $group)
    {
        global $CFG;

        $data = array(
            'termCode'    => $term,
            'lessonCode'  => $lesson,
            'lessonGroup' => $group
        );
        $params = http_build_query($data, null, "&");

        // API URL
        $url = $CFG->samaurl.'/services/EducationService.svc/web/2019/01/GetTermLessonList?'.$params;

        // Create a new cURL resource
        $ch = curl_init($url);

        // Return response instead of outputting
        $cookies = [
            "Cookie: ".$this->auth->name."=".$this->auth->value,
            "Cookie: ".$this->asp_auth->name."=".$this->asp_auth->value
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cookies);

        // Execute the POST request
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        if ($result == '"این لیست موجود نیست"') {
            $msg = "این لیست موجود نمی باشد.";
            return array('status' => Status::END, 'msg' => $msg);
        }
        // Close cURL resource
        curl_close($ch);

        $lessons = json_decode($result);
        // $user = new Student("asd","asd","student7","asd","asd");

        $courses = array();
        // $study_levels = array(5, 6, 7, 8);
        if (is_object($lessons) || is_array($lessons)) {

            foreach ($lessons as $lesson) {
                $tlesson = $lesson;
                if ($tlesson->Lesson->LessonCode) {
                    $groupTitle = "گروه درسی ".(int) $tlesson->LessonGroup;
                    $idnumber = $term."-".
                        ((int) $tlesson->LessonGroup)."-".
                        $tlesson->Lesson->LessonCode;
                    $shortname = $idnumber."-".$tlesson->Lesson->LessonName;
                    $fullname = $tlesson->Lesson->LessonName;
                    $category = new Category(
                        (string)$tlesson->FacultyCode,
                        ($tlesson->FacultyTitle ?: 'دانشکده ' . $tlesson->FacultyCode),
                        (int) $tlesson->LessonGroup,
                        $groupTitle
                    );

                    $course = new Course($idnumber, $fullname, $shortname,
                        $category);

                    $courses[] = $course;
                }
            }

            $courses = $this->array_unique_objects($courses);
        } else {
            $msg = "به علت عدم پاسخگویی سرور سامانه سما، دریافت اطلاعات با مشکل مواجه شده است."." کد: LI1";
            logit($url);
            logit($lessons);
            return array('status' => Status::ERROR, 'msg' => $msg, 'items' => $courses);

        }

        //$courses = array();
        $msg = "در حال ثبت دوره";
        return array(
            'status' => Status::SUCCESS, 'msg' => $msg, 'sec_key' => Settings::$security_key,
            'items'  => $courses
        );

    }

    public function array_unique_objects($array, $keep_key_assoc = false)
    {
        $duplicate_keys = array();
        $tmp = array();

        foreach ($array as $key => $val) {
            // convert objects to arrays, in_array() does not support objects
            if (is_object($val)) {
                $val = (array) $val;
            }

            if (!in_array($val, $tmp)) {
                $tmp[] = $val;
            } else {
                $duplicate_keys[] = $key;
            }
        }

        foreach ($duplicate_keys as $key) {
            unset($array[$key]);
        }

        return $keep_key_assoc ? $array : array_values($array);
    }

    public function login()
    {
        global $CFG;

        $data = array(
            'username' => $CFG->samauser,
            'password' => $CFG->samapass
        );

        $params = http_build_query($data, null, "&");
        $url = $CFG->samaurl.'/services/AuthenticationService.svc/web/Login?'.$params;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        $cookies = array();
        preg_match_all('/Set-Cookie:(?<cookie>\s{0,}.*)$/im', $result, $cookies);

        // var_dump(http_parse_cookie($cookies['cookie']));
        //var_dump($cookies['cookie']);
        $auth_coockies = $this->parse_cookies($cookies['cookie'][0]);
        $asp_coockies = $this->parse_cookies($cookies['cookie'][1]);
        $cookieParts = array();
        preg_match_all('/Set-Cookie:\s{0,}(?P<name>[^=]*)=(?P<value>[^;]*).*?/im', $result, $cookieParts);

        curl_close($ch);
        $this->auth = $auth_coockies[0];
        $this->asp_auth = $asp_coockies[0];

        return $result;
    }

    public function parse_cookies($header)
    {

        $cookies = array();

        $cookie = new Cookie();

        $parts = explode("=", $header);
        $key = "";
        for ($i = 0; $i < count($parts); $i++) {
            $part = $parts[$i];
            if ($i == 0) {
                $key = $part;
                continue;
            } elseif ($i == count($parts) - 1) {
                $cookie->set_value($key, $part);
                $cookies[] = $cookie;
                continue;
            }
            $comps = explode(" ", $part);
            $new_key = $comps[count($comps) - 1];
            $value = substr($part, 0, strlen($part) - strlen($new_key) - 1);
            $terminator = substr($value, -1);
            $value = substr($value, 0, strlen($value) - 1);
            $cookie->set_value($key, $value);
            if ($terminator == ",") {
                $cookies[] = $cookie;
                $cookie = new Cookie();
            }

            $key = $new_key;
        }
        return $cookies;
    }

}
