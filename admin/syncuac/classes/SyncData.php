<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 10/30/2020
 * Time: 1:05 PM
 */

namespace Synchronizer\Classes;

use core_course_category;
use Exception;
use stdClass;
use Synchronizer\Settings;

require_once('../../config.php');
require_once('settings.php');
/** @noinspection PhpUndefinedVariableInspection */
require_once $CFG->dirroot.'/user/lib.php';
require_once $CFG->dirroot.'/course/lib.php';

class SyncData
{
    public $_conn;
    private $role_text;
    private $isMockup = false;

    public function __construct()
    {
        $this->role_text = array(
            Settings::$student_role_id => 'دانشپذیر',
            Settings::$teacher_role_id => 'استاد',
        );
    }

    public function setMockup($mockup)
    {
        $this->isMockup = $mockup;
    }

    public function getUser($username)
    {
        global $DB;
        try {
            $user = $DB->get_record('user', array('username' => $username));
            return array('status' => Status::SUCCESS, 'msg' => '', 'items' => $user);
        } catch (Exception $e) {
            $msg = $this->formatMsg("خطا در دریافت ".$username, TextType::ERROR)."<br>";
            $msg .= $this->formatMsg("ERROR: ".$e->getMessage(), TextType::ERROR, "text-left");
            return array('status' => Status::ERROR, 'msg' => $msg, 'items' => []);
        }

    }

    public function insertUsers($student, $index = 0, $update = false)
    {
        global $CFG, $DB;
        $user = json_decode(json_encode($student, JSON_UNESCAPED_UNICODE), false);

        $user->mnethostid = $CFG->mnet_localhost_id; // always local user

        $user->password = $user->national_code;
        $pass = $user->password;
        $forceUpdate = false;
        try {
            $olduser = $DB->get_record('user', ['username' => $user->username]);
            $userWithNationalCode = null;
            if ($CFG->usenationalcodeasusername && $user->national_code !== $user->username) {
                $userWithNationalCode = $DB->get_record('user', ['username' => $user->national_code]);
            }
            if ($olduser && $userWithNationalCode) {
                $msg = $this->formatMsg("خطا در ثبت ".$user->username, TextType::ERROR)."<br>";
                $msg .= $this->formatMsg("کد ملی ".$user->national_code, TextType::ERROR)."<br>";
                $msg .= $this->formatMsg("ERROR: "."دو کاربر با اطلاعات یکسان یا شد", TextType::ERROR, "text-left");
                return array('status' => Status::ERROR, 'msg' => $msg);
            }

            if (!$userWithNationalCode && $olduser) {
                $forceUpdate=true;
                $user->username = $user->national_code;
            }

            //$olduser =false;
            if ($olduser) {
                if ($update || $forceUpdate) {
                    $user->id = $olduser->id;

                    if (Settings::$update_password && !$forceUpdate) {
                        $user->password = $user->national_code;

                    } else {
                        unset($user->password);
                        $pass = '';
                    }
                    $user->institution = 1;
                    user_update_user($user, Settings::$update_password);

                    $msg = $this->formatMsg($user->firstname." ".$user->lastname, TextType::INFO);
                    $msg .= $this->formatMsg(
                        " (<span class='text-success'>$user->username</span>) "." با موفقیت ویرایش شد. "
                        ." - رمز عبور : ");
                    $msg .= $this->formatMsg($pass, TextType::ERROR);

                    //                    return $this->printInDiv($msg, $index, Status::SUCCESS);
                    return array('status' => Status::EDIT, 'msg' => $msg);

                } else {
                    $msg = $this->formatMsg($user->firstname." ".$user->lastname, TextType::INFO);
                    $msg .= $this->formatMsg(
                        " (<span class='text-success'>$user->username</span>) "." قبلا ثبت شده است. ");
                    return array('status' => Status::SKIP, 'msg' => $msg);
                }

            } else {
                $user->user_name = $user->national_code;
                $user->id = user_create_user($user);
                $user->password = $user->national_code;

                $msg = $this->formatMsg($user->firstname." ".$user->lastname, TextType::INFO);
                $msg .= $this->formatMsg(
                    " (<span class='text-success'>$user->username</span>) "." با موفقیت ثبت شد. "." - رمز عبور : ");
                $msg .= $this->formatMsg($user->password, TextType::ERROR);

                return array('status' => Status::SAVE, 'msg' => $msg);

            }

        } catch (Exception $e) {
            $msg = $this->formatMsg("خطا در ثبت ".$user->username, TextType::ERROR)."<br>";
            $msg .= $this->formatMsg("ERROR: ".$e->getMessage(), TextType::ERROR, "text-left");
            return array('status' => Status::ERROR, 'msg' => $msg);
        }

    }

    public function insertTeachers($teacher, $update = false)
    {
        global $CFG, $DB;
        $user = json_decode(json_encode($teacher, JSON_UNESCAPED_UNICODE), false);

        $user->mnethostid = $CFG->mnet_localhost_id; // always local user

        $user->password = $user->national_code;
        $pass = $user->password;
        //$user->email = $user->national_code . "@tabrizu.ac.ir";

        try {

            $olduser = $DB->get_record('user', array('username' => $user->username));

            if ($olduser) {
                if ($update) {
                    $user->id = $olduser->id;
                    if (Settings::$update_password) {
                        $user->password = $user->national_code;
                    } else {
                        unset($user->password);
                        $pass = 'بدون تغییر';
                    }
                    user_update_user($user, Settings::$update_password);
                    $msg = "<span class='text-info'>".$user->firstname." ".$user->lastname."</span>".
                        " (<span class='text-success'>$user->username</span>) "." با موفقیت ویرایش شد. "
                        ." - رمز عبور : ".
                        "<span class='text-danger'>$pass</span>";
                    return array('status' => Status::SAVE, 'msg' => $msg);

                } else {
                    $msg = "<span class='text-info'>".$user->firstname." ".$user->lastname."</span>".
                        " (<span class='text-success'>$user->username</span>) "." قبلا ثبت شده است. ";
                    return array('status' => Status::SKIP, 'msg' => $msg);
                }

            } else {
                $user->id = user_create_user($user);
                $user->password = $user->national_code;
                $msg = "<span class='text-info'>".$user->firstname." ".$user->lastname."</span>".
                    " (<span class='text-success'>$user->username</span>) "." با موفقیت ثبت شد. "." - رمز عبور : ".
                    "<span class='text-danger'>$user->password</span>";
                return array('status' => Status::EDIT, 'msg' => $msg);
            }

        } catch (Exception $e) {
            $msg = "<span class='text-danger'>"."خطا در ثبت ".$user->username.
                "</span><br> <span class='text-danger text-left'>ERROR: ".$e->getMessage()."</span>";
            return array('status' => Status::ERROR, 'msg' => $msg);
        }

    }

    public function insertLesson($term, $lesson, $update = false)
    {

        try {
            $lesson = json_decode(json_encode($lesson, JSON_UNESCAPED_UNICODE), false);
            $tcode = 'TRM'.$term;
            $fcode = $term.'10'.$lesson->categoryData->facultyCode;
            $ccode = '20'.$fcode.$lesson->categoryData->lessonGroupId;
            $termCat = $this->check_category(
                $term,
                $tcode, 0);
            $UniName = $this->check_category(
                $lesson->categoryData->facultyTitle,
                $fcode, $termCat);
            $MainCategory = $this->check_category(
                $lesson->categoryData->groupTitle,
                $ccode, $UniName);
            $lesson->category = $MainCategory;
            $result = $this->check_lesson($lesson);
            if ($result == Status::SAVE) {
                $msg = "درس  "."<span class='text-primary'>".$lesson->shortname."</span>"." با موفقیت ثبت شد";
                return array('status' => Status::SAVE, 'msg' => $msg);
            } elseif ($result == Status::EDIT) {
                $msg = "درس  "."<span class='text-primary'>".$lesson->shortname."</span>"." با موفقیت بروزرسانی شد";
                return array('status' => Status::EDIT, 'msg' => $msg);
            }

        } catch (Exception $e) {
            $msg = "<span class='text-danger'>"." خطا در ثبت درس به شماره ".$lesson->idnumber.
                "</span><br> <span class='text-danger text-left'>ERROR: "
                .$e->getMessage()."</span>";
            return array('status' => Status::ERROR, 'msg' => $msg);
        }
        $msg = "<span class='text-danger'>"." خطا در ثبت درس به شماره ".$lesson->idnumber.
            "</span>";
        return array('status' => Status::ERROR, 'msg' => $msg);
    }

    public function switch_cat($array)
    {
        try {
            $term = '14002';
            foreach ($array as $cid) {
                $courses = $DB->get_recordset_select('course', "idnumber LIKE '{$term}-{$cid}'", null, '', 'idnumber',
                    $start,
                    $items_per_page);
            }

            $lessons = $array;
            $tcode = 'TRM'.$term;
            $fcode = $term.'10'.$lesson->categoryData->facultyCode;
            $ccode = '20'.$fcode.$lesson->categoryData->lessonGroupId;
            $termCat = $this->check_category(
                $term,
                $tcode, 0);
            $UniName = $this->check_category(
                $lesson->categoryData->facultyTitle,
                $fcode, $termCat);
            $MainCategory = $this->check_category(
                $lesson->categoryData->groupTitle,
                $ccode, $UniName);
            $lesson->category = $MainCategory;
            $result = $this->check_lesson($lesson);
            if ($result == Status::SAVE) {
                $msg = "درس  "."<span class='text-primary'>".$lesson->shortname."</span>"." با موفقیت ثبت شد";
                return array('status' => Status::SAVE, 'msg' => $msg);
            } elseif ($result == Status::EDIT) {
                $msg = "درس  "."<span class='text-primary'>".$lesson->shortname."</span>"." با موفقیت بروزرسانی شد";
                return array('status' => Status::EDIT, 'msg' => $msg);
            }

        } catch (Exception $e) {
            $msg = "<span class='text-danger'>"." خطا در ثبت درس به شماره ".$lesson->idnumber.
                "</span><br> <span class='text-danger text-left'>ERROR: "
                .$e->getMessage()."</span>";
            return array('status' => Status::ERROR, 'msg' => $msg);
        }
        $msg = "<span class='text-danger'>"." خطا در ثبت درس به شماره ".$lesson->idnumber.
            "</span>";
        return array('status' => Status::ERROR, 'msg' => $msg);
    }

    public function saveEnrollments($enrollments, $delete, $roleid = null)
    {
        global $DB;
        $table = Settings::$enrol_table;
        $responses = [];
        //
        $rows = $enrollments;

        foreach ($rows as $index => $row) {

            if ((!isset($row->username) || empty($row->username)) || !$row->courseid) {

                $msg = $this->formatMsg(
                    "خطا در ثبت نام، اطلاعات داده شده کافی نمی باشد: "."$row->username , $row->courseid"
                    , TextType::ERROR);
                $responses[] = array('status' => Status::ERROR, 'msg' => $msg);
                continue;
            }

            $conditions = array(
                'courseid' => $row->courseid,
                'term'     => $row->term,
                'username' => $row->username,
            );
            try {
                $enrol = $DB->get_record($table, $conditions);
                $id = null;

                if ($enrol) {
                    $id = $enrol->id;
                    $enrol->is_updated = 1;
                }
                if ($id) {
                    $DB->update_record($table, $enrol);
                    $update_msg = "بروزرسانی";
                } else {
                    $DB->insert_record($table, $row);
                    $update_msg = "انجام";
                }

                $msg = $this->formatMsg(" ثبت نام کاربر با کد ");
                $msg .= $this->formatMsg($row->username, TextType::PRIMARY);
                $msg .= $this->formatMsg(" با موفقیت در درس ");
                $msg .= $this->formatMsg($row->courseid, TextType::SUCCESS);
                $msg .= $this->formatMsg(" با نقش ");
                $msg .= $this->formatMsg($this->role_text[$row->roleid], TextType::ERROR);
                $msg .= $this->formatMsg(" ".$update_msg." شد");
                if ($id) {
                    $responses[] = array('status' => Status::EDIT, 'msg' => $msg);
                } else {
                    $responses[] = array('status' => Status::SAVE, 'msg' => $msg);
                }

            } catch (Exception $e) {


                $msg = $this->formatMsg(" خطا در ثبت نام کاربر ".$row->username.
                        " در درس ".$row->courseid, TextType::ERROR)."<br>";
                $msg .= $this->formatMsg("ERROR: ".$e->getMessage(), TextType::ERROR, "text-left");

                $responses[] = array('status' => Status::ERROR, 'msg' => $msg);
            }

        }

        try {

            switch ($delete) {
                case "username":
                    $DB->delete_records(Settings::$enrol_table,
                        array('username' => $rows[0]->username, 'term' => $rows[0]->term, 'is_updated' => 0));

                    $DB->set_field(Settings::$enrol_table, 'is_updated', 0, array('username' => $rows[0]->username));
                    $msg = $this->formatMsg("ثبت نام کاربر ".$rows[0]->username." ویرایش شد",
                        TextType::INFO);
                    $responses[] = array('status' => Status::SUCCESS, 'msg' => $msg);
                    break;
                case "courseid":
                    if ($roleid) {
                        $DB->delete_records(Settings::$enrol_table,
                            array('courseid' => $rows[0]->courseid, 'is_updated' => 0, 'roleid' => $roleid));
                    } else {
                        $DB->delete_records(Settings::$enrol_table,
                            array('courseid' => $rows[0]->courseid, 'is_updated' => 0));
                    }
                    $DB->set_field(Settings::$enrol_table, 'is_updated', 0, array('courseid' => $rows[0]->courseid));

                    if ($roleid == Settings::$teacher_role_id) {
                        $msg = $this->formatMsg("ثبت نام اساتید دوره ".$rows[0]->courseid." ویرایش شد",
                            TextType::INFO);
                    } elseif ($roleid == Settings::$student_role_id) {
                        $msg = $this->formatMsg("ثبت نام دانشپذیران دوره ".$rows[0]->courseid." ویرایش شد",
                            TextType::INFO);
                    } else {
                        $msg = $this->formatMsg("ثبت نام دانشپذیران و اساتید دوره ".$rows[0]->courseid." ویرایش شد",
                            TextType::INFO);
                    }

                    $responses[] = array('status' => Status::SUCCESS, 'msg' => $msg);
                    break;
                default:
                    break;
            }

        } catch (Exception $e) {
            $msg = $this->formatMsg(" خطا در حذف ثبت نام درس".$rows[0]->courseid, TextType::ERROR)."<br>";
            $msg .= $this->formatMsg("ERROR: ".$e->getMessage(), TextType::ERROR, "text-left");

            $responses[] = array('status' => Status::ERROR, 'msg' => $msg);
        }

        return $responses;

    }

    public function saveLessonEnrollments($enrollments, $delete, $isJSON = true)
    {
        global $DB;
        $table = Settings::$enrol_table;

        //
        if ($isJSON) {
            $rows = json_decode(json_encode($enrollments, JSON_UNESCAPED_UNICODE), false);
        } else {
            $rows = $enrollments;
        }

        $len = count($rows);
        foreach ($rows as $index => $row) {

            if ((!isset($row->username) || empty($row->username)) || !$row->courseid) {

                $msg = $this->formatMsg(
                    "خطا در ثبت نام، اطلاعات داده شده کافی نمی باشد: "."$row->username , $row->courseid"
                    , TextType::ERROR);
                echo $this->printInDiv($msg, $index, Status::ERROR);
                continue;
            }

            $conditions = array(
                'courseid' => $row->courseid,
                'term'     => $row->term,
                'username' => $row->username,
            );
            try {
                $enrol = $DB->get_record($table, $conditions);
                $id = null;

                if ($enrol) {
                    $id = $enrol->id;
                    $enrol->is_updated = 1;
                }
                if ($id) {
                    $DB->update_record($table, $enrol);
                    $update_msg = "بروزرسانی";
                    $sts = Status::EDIT;
                } else {
                    $DB->insert_record($table, $row);

                    $update_msg = "انجام";
                    $sts = Status::SAVE;
                }

                $msg = $this->formatMsg(" ثبت نام کاربر با کد ");
                $msg .= $this->formatMsg($row->username, TextType::PRIMARY);
                $msg .= $this->formatMsg(" با موفقیت در درس ");
                $msg .= $this->formatMsg($row->courseid, TextType::SUCCESS);
                $msg .= $this->formatMsg(" با کد نقش ");
                $msg .= $this->formatMsg($row->roleid, TextType::ERROR);
                $msg .= $this->formatMsg($update_msg." شد");

                echo $this->printInDiv($msg, $index, $sts);

            } catch (Exception $e) {


                $msg = $this->formatMsg(" خطا در ثبت نام کاربر ".$row->username.
                        " در درس ".$row->courseid, TextType::ERROR)."<br>";
                $msg .= $this->formatMsg("ERROR: ".$e->getMessage(), TextType::ERROR, "text-left");

                echo $this->printInDiv($msg, $index, Status::ERROR);
            }
            echo str_repeat(' ', 1024 * 64);

            //usleep(50000);

        }
        try {
            switch ($delete) {
                case "username":
                    $DB->delete_records(Settings::$enrol_table,
                        array('username' => $rows[0]->username, 'is_updated' => 0));

                    $DB->set_field($table, 'is_updated', 0, array('username' => $rows[0]->username));
                    $msg = $this->formatMsg("ثبت نام کاربر ".$rows[0]->username." ویرایش شد",
                        TextType::INFO);
                    echo $this->printInDiv($msg, -1, Status::SUCCESS);
                    break;
                case "courseid":
                    $DB->delete_records(Settings::$enrol_table,
                        array('courseid' => $rows[0]->courseid, 'is_updated' => 0));
                    $DB->set_field($table, 'is_updated', 0, array('courseid' => $rows[0]->courseid));

                    $msg = $this->formatMsg("ثبت نام دانشپذیران دوره ".$rows[0]->courseid." ویرایش شد",
                        TextType::INFO);

                    echo $this->printInDiv($msg, -1, Status::SUCCESS);
                    break;
                default:
                    break;
            }

        } catch (Exception $e) {
            $msg = $this->formatMsg(" خطا در حذف ثبت نام درس".$rows[0]->courseid, TextType::ERROR)."<br>";
            $msg .= $this->formatMsg("ERROR: ".$e->getMessage(), TextType::ERROR, "text-left");

            echo $this->printInDiv($msg, -1, Status::ERROR);
        }
        // echo str_repeat(' ', 1024 * 64);
        ob_flush();
        flush();

    }

    public function check_category($catname, $catidnumber, $parent)
    {
        global $DB;
        try {
            $category = $DB->get_record('course_categories', array('idnumber' => $catidnumber));

            if (!$category) {

                $data = new stdClass();
                $data->parent = $parent;
                $data->name = $catname;
                $data->idnumber = $catidnumber;

                $category = core_course_category::create($data);
            }

            return $category->id;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());

        }

    }

    function check_lesson($lesson)
    {
        global $DB;

        try {
            $course = $DB->get_record('course', array('idnumber' => $lesson->idnumber));

            if (!$course) {

                create_course($lesson);
                $msg = Status::SAVE;
            } else {

                $lesson->id = $course->id;
                update_course($lesson);
                $msg = Status::EDIT;
            }
            return $msg;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

    }

    public function syncCategory($category)
    {
        if (!$this->_conn) {
            return null;
        }
        $table = Settings::$category_table;
        //$row = json_decode(json_encode($categories, JSON_UNESCAPED_UNICODE), FALSE);
        $id = $this->selectCategory($category->center_id);

        if ($id) {
            $query = "UPDATE $table SET";
            $query .= "name = :name, idnumber = :idnumber";
            $query .= "WHERE id = ':id'";
        } else {
            $cols = "(name, idnumber, parent)";
            $query = "INSERT INTO $table ";
            $query .= $cols;
            $values = "(:name,:idnumber,0)";
            $query .= " VALUES ".$values;

        }

        try {

            $stm = $this->_conn->prepare($query);
            $stm->bindParam(":name", $category->center_name);
            $stm->bindParam(":idnumber", $category->center_id);
            if ($id) {
                $stm->bindParam(":id", $id);
            }
            $this->_conn->beginTransaction();
            $stm->execute();
            $this->_conn->commit();
            if ($id) {
                return $id;
            } else {
                return $this->_conn->lastInsertId();

            }

        } catch (PDOException $e) {
            return null;
        }

    }

    public function unEnroll($obj)
    {
        global $DB;
        $row = json_decode(json_encode($obj, JSON_UNESCAPED_UNICODE), false);
        if ($row->action == 'courses') {
            $role = Settings::$teacher_role_id;
            $msg = "<span class='text-info'>"."ثبت نام اساتید در دوره".$row->course." ویرایش شد</span>";

        } elseif ($row->action == 'enrol') {
            $role = Settings::$student_role_id;
            $msg = "<span class='text-info'>"." ثبت نام دانشجویان در دوره".$row->course." ویرایش شد </span>";

        } else {
            return "Failed No Action";
        }
        try {
            $table = Settings::$enrol_table;
            $result = $DB->delete_records($table,
                array('courseid' => $row->course, 'is_updated' => 0, 'roleid' => $role));

            $result = $DB->set_field($table, 'is_updated', 0, array('courseid' => $row->course, 'roleid' => $role));

            return $msg;

        } catch (dml_exception $e) {
            return $msg = "<span class='text-danger'>"."خطا در حذف اطلاعات".$e->getMessage()."</span>";
        }

    }

    public function deleteRecords($table, array $conditions = null)
    {
        global $DB;
        $DB->delete_records($table, $conditions);
    }

    function printInDiv($text, $index, $status = 1, $isHeader = false)
    {

        $cls = 'info';
        if ($isHeader) {
            $cls = 'info-header';
        }
        if ($index != -1) {
            $cls .= " indexed";
            $index += 1;
        }

        return "<div class='d-block $cls i-$index' data-status='$status' data-index='$index'>$text</div>";
    }

    function formatMsg($text, $type = null, $class = "")
    {

        $msg = "";
        switch ($type) {
            case TextType::INFO:
                $msg = "<span class='text-info $class'>".$text."</span>";
                break;
            case TextType::ERROR:
                $msg = "<span class='text-danger $class'>".$text."</span>";
                break;
            case TextType::SUCCESS:
                $msg = "<span class='text-success $class'>".$text."</span>";
                break;
            case TextType::PRIMARY:
                $msg = "<span class='text-primary $class'>".$text."</span>";
                break;
            default:
                $msg = "<span class='$class'>".$text."</span>";
                break;
        }

        return $msg;
    }
}
//$dd = new SyncData();
//$dd->syncEnrolment(null,null);
