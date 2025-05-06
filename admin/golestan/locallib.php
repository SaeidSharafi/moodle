<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 10/30/2020
 * Time: 1:05 PM
 */

require_once "settings.php";
require_once $CFG->dirroot.'/course/lib.php';

class SyncDB
{
    public $_conn;

    const SAVE = 1;
    const EDIT = 2;


    private function connect()
    {
        $connection = "mysql:host=".Config::$db_server.";dbname=".Config::$db_name.";charset=utf8";
        if (Config::$mssql) {
            $connection = "sqlsrv:Server=".Config::$db_server.";Database=".Config::$db_name;
        }

        try {
            $this->_conn = new PDO($connection, Config::$db_user, Config::$db_pass);
            return true;
        } catch (Exception $e) {
            return ("خطا در اتصال به دیتابیس:<br> ".$e->getMessage());
        }
    }

    public function insertUsers($students, $update = false)
    {
        global $DB;

        $table = Config::$user_table;
        $row = json_decode(json_encode($students, JSON_UNESCAPED_UNICODE), false);
        $user_id = $this->selectUser($row->id);

        if ($row->meli) {
            $pass = $row->meli;
        } else {
            $pass = $row->id."@pass";
        }
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        $time = time();
        if ($update && $user_id) {
            $query = " UPDATE {{$table}} SET ";
            $query .= "firstname = :fname, lastname =  :lname, email = :email, timemodified = :time";
            $query .= " WHERE username = :id";
        } else {
            if ($user_id) {

                return "کاربر با نام کاربری ".$row->id." قبلا ثبت شده است";
            }
            $cols = "(auth, confirmed, mnethostid, username, password,".
                " firstname, lastname, email, lang, calendartype, timezone, timecreated, timemodified)";
            $query = "INSERT INTO {{$table}} ";
            $query .= $cols;
            $query .= " VALUES ('manual',1,1,:id,:pass,:fname,:lname,:email,'fa','jalali','99',:time,'0')";

        }
        try {

            $stmt = $this->_conn->prepare($query);
            if (!($update && $user_id)) {
                $stmt->bindParam(":pass", $hashed_pass);
            }
            $stmt->bindParam(":id", $row->id);
            $stmt->bindParam(":fname", $row->fname);
            $stmt->bindParam(":lname", $row->lname);
            $stmt->bindParam(":email", strtolower($row->email));
            $stmt->bindParam(":time", $time);

            $this->_conn->beginTransaction();
            $stmt->execute();
            $this->_conn->commit();

            return "<span class='text-info'>".$row->fname." ".$row->lname."</span>".
                " (<span class='text-success'>$row->id</span>) "." با موفقیت ثبت شد. "." - رمز عبور : ".
                "<span class='text-danger'>$pass</span>";

        } catch (Exception $e) {

            return "<span class='text-danger'>"."خطا در ثبت ".$row->id.
                "</span><br> <span class='text-danger text-left'>ERROR: ".$e->getMessage()."</span>";
        }

    }

    public function syncCourses($course, $update = false, $params = false)
    {
        global $DB;

        $table = Config::$course_table;
        $row = json_decode(json_encode($course, JSON_UNESCAPED_UNICODE), false);

        if ($params) {
            //$parent_category = $params['category'] ?: 0;
            $term = $params['term'] ?: 'TRM';
        }
        if (!$row->id) {
            return "درس بدون کد : ".$row->name;
        }
        //$row = $course;

        $name = $row->id."-".$row->name."-".$row->group."-".str_replace('درس(ت):', '', $row->time);
        $shortname = $row->id."_".$row->group."_".$row->center_id."_".$row->term;
        $idnumber = $row->center_id.$row->id.$row->group.$row->term;
        $cat_idnumber = $term.$row->center_id;
        $parent_id = $this->check_category(
            $term,
            $term, 0);
        $cat_id = $this->check_category(
            $row->center_name,
            $cat_idnumber, $parent_id);

        if ($cat_id instanceof Exception) {
            return "<span class='text-danger'>"." خطا در ثبت درس به شماره ".$row->id.
                "</span><br> <span class='text-danger text-left'>ERROR: "
                .$cat_id->getMessage()."</span>";
        }
        //$cat_id = $this->syncCategory($row);
        //$id = $this->selectCourse($idnumber, $cat_id);
        $lesson = new stdClass();
        $lesson->idnumber = $idnumber;
        $lesson->fullname = $name;
        $lesson->shortname = $shortname;
        $lesson->category = $cat_id;
        $result = $this->check_lesson($lesson);
        if ($result instanceof Exception) {
            return "<span class='text-danger'>"." خطا در ثبت درس به شماره ".$row->id.
                "</span><br> <span class='text-danger text-left'>ERROR: "
                .$result->getMessage()."</span>";
        }

        if ($result == SyncDB::SAVE) {
            $msg = "درس  "."<span class='text-primary'>".$name."</span>"." با موفقیت ثبت شد";
        } elseif ($result == SyncDB::EDIT) {
            $msg = "درس  "."<span class='text-primary'>".$name."</span>"." با موفقیت ویرایش شد";
        }
        //$msg = "درس  " . "<span class='text-primary'>" . $name . "</span>" . " با موفقیت ثبت شد";
        $enroll['user_id'] = $row->teacher_id;
        $enroll['crs_id'] = $row->id;
        $enroll['center_id'] = $row->center_id;
        $enroll['group'] = $row->group;
        $enroll['term'] = $row->term;
        $msg .= "<br>";
        $msg .= $this->syncEnrolment($enroll, Config::$teacher_role_id);

        return $msg;

    }

    public function syncEnrolment($enrollment, $roleid = 5)
    {
       global $DB;
        $table = Config::$enrol_table;
        $today = date("Y-m-d");
        $row = json_decode(json_encode($enrollment, JSON_UNESCAPED_UNICODE), false);
        if (!$row->user_id || !$row->crs_id) {
            return "<span class='text-danger'>"."خطا در ثبت نام، اطلاعات داده شده کافی نمی باشد: ".
                "$row->user_id , $row->crs_id"."</span>";
        }

        $idnumber = $row->center_id.$row->crs_id.$row->group.$row->term;
        $id = $this->selectEnrollment($row, $idnumber);
        if ($id) {
            $query = "UPDATE {{$table}} SET ";
            $query .= "enrolldate = :today, is_updated = 1";
            $query .= "WHERE id = $id";
            $update_msg = "بروزرسانی";
        } else {
            $query = "INSERT INTO {{$table}} ";
            $query .= "(username, courseid, roleid, term, enrolldate, center, is_updated)";
            $query .= " VALUES (:user_id,:crs_id,:roleid,:term,:today,:center,1)";
            $update_msg = "انجام";
        }


        try {
            $transaction = $DB->start_delegated_transaction();
            $DB->execute($query, [
                'user_id' => $row->user_id,
                'crs_id' => $idnumber,
                'roleid' => $roleid,
                'term' => $row->term,
                'center' =>  $row->center_id,
                'today' => $today,
            ]);
            $transaction->allow_commit();
            $msg = " ثبت نام کاربر با کد "."<span class='text-primary'>".$row->user_id."</span>".
                " با موفقیت در درس "."<span class='text-success'>".$row->crs_id."</span>"
                ." با کد نقش "."<span class='text-danger'>".$roleid."</span>".$update_msg." شد";
            return $msg;

        } catch (\Exception $e) {
            $transaction->rollback($e);
            return "<span class='text-danger'>"." خطا در ثبت نام کاربر ".$row->std_id.
                " در درس ".$row->crs_id.
                "</span><br><span class='text-danger text-left'> ERROR: ".$e->getMessage()."</span>";
        }

    }

    public function syncCategory($category)
    {
       global $DB;
        $table = Config::$category_table;
        //$row = json_decode(json_encode($categories, JSON_UNESCAPED_UNICODE), FALSE);
        $id = $this->selectCategory($category->center_id);

        if ($id) {
            $query = "UPDATE {{$table}} SET";
            $query .= "name = :name, idnumber = :idnumber";
            $query .= "WHERE id = ':id'";

        } else {
            $cols = "(name, idnumber, parent)";
            $query = "INSERT INTO {{$table}} ";
            $query .= $cols;
            $values = "(:name,:idnumber,0)";
            $query .= " VALUES ".$values;

        }

        try {

           $conditions = [
               'name' => $category->center_name,
               'idnumber' => $category->center_id,
           ];
            if ($id) {
                array_push($conditions,[
                    'id' => $id
                ]);
            }
             $transaction = $DB->start_delegated_transaction();
            $DB->execute($query,);
            $transaction->allow_commit();
            if ($id) {
                return $id;
            } else {
                return $this->_conn->lastInsertId();

            }

        } catch (Exception $e) {
            $transaction->rollback($e);
            return null;
        }

    }

    public function selectUser($username)
    {
        global $DB;

        try {
            $value = $DB->get_record(Config::$user_table, ['username' => $username]);

            if ($value) {
                return $value;
            }
        } catch (Exception $e) {
            return "ERROR";
        }

        return null;

    }

    public function selectEnrollment($enroll, $idnumber)
    {
        global $DB;
        try {
            $value = $DB->get_record(Config::$enrol_table, [
                'username' => $enroll->user_id,
                'term'     => $idnumber,
                'courseid' => $enroll->center_id,
                'center'   => $enroll->term,
            ]);

            if ($value) {
                return $value;
            }
        } catch (Exception $e) {
            throw $e;
        }

        return null;

    }

    public function selectCategory($id)
    {
        global $DB;

        try {
            $value = $DB->get_record(Config::$category_table, [
                'idnumber' => $id,
            ]);
            if ($value) {
                return $value;
            }
        } catch (Exception $e) {
            return "ERROR";
        }
        return null;

    }

    public function selectCourse($id, $cat_id)
    {
        if (!$this->_conn) {
            return null;
        }
        $table = Config::$course_table;
        $query = "SELECT ";
        if (Config::$mssql == true) {
            $query .= "TOP 1 ";
        }
        $query .= "id FROM {{$table}} WHERE idnumber = :idnumber";
        if (!Config::$mssql) {
            $query .= " limit 1";
        }
        try {
            $stm = $this->_conn->prepare($query);
            $stm->bindParam(":idnumber", $id);
            //$stm->bindParam(":cat_id", $cat_id);
            $stm->execute();
            $value = $stm->fetchColumn();

            if ($value) {
                return $value;
            }
        } catch (Exception $e) {
            return "ERROR";
        }
        return null;

    }

    public function unEnroll($obj, $params)
    {
        global $DB;
        $table = Config::$enrol_table;
        $term = $params['term'];

        if (!$term) {
            $msg = "<span class='text-info'>کد ترم یافت نشد</span>";
            return $msg;
        }
        $today = date("Y-m-d");
        $row = json_decode(json_encode($obj, JSON_UNESCAPED_UNICODE), false);

        if ($row->action == 'courses') {
            $role = Config::$teacher_role_id;
            $msg = "<span class='text-info'>"."تعداد ثبت نام حذف شده از اساتید در مرکز".$row->center."در ترم ".$term.
                "</span>";

        } elseif ($row->action == 'enroll') {
            $role = Config::$student_role_id;
            $msg = "<span class='text-info'>"."تعداد ثبت نام حذف شده از دانشجویان در مرکز".$row->center."در ترم ".
                $term."</span>";

        } else {
            return "Failed No Action";
        }

        try {
            $transaction = $DB->start_delegated_transaction();
            $DB->delete_records($table,
                [
                    'roleid'     => $role,
                    'term'       => $term,
                    'center'     => $row->center,
                    'is_updated' => 0,
                ]);

            $msg = $msg." : ".$stm->rowCount();

            $query = "UPDATE {{$table}} SET is_updated = 0 WHERE roleid = :role AND term = :term AND center = :center ";
            $DB->execute($query, [
                'role'   => $role,
                'term'   => $term,
                'center' => $row->center,
            ]);
           $transaction->allow_commit();
            return $msg;

        } catch (Exception $e) {
            $transaction->rollback($e);
            return $msg = "<span class='text-danger'>"."خطا در حذف اطلاعات"."</span>";
        }

    }

    public function check_category($catname, $catidnumber, $parent)
    {
        global $DB;
        try {
            $category = $DB->get_record('course_categories', array('idnumber' => $catidnumber, 'parent' => $parent));

            if (!$category) {

                $data = new stdClass();
                $data->parent = $parent;
                $data->name = $catname;
                $data->idnumber = $catidnumber;

                $category = core_course_category::create($data);
            }

            return $category->id;
        } catch (Exception $e) {
            return $e;

        }

    }

    function check_lesson($lesson)
    {
        global $DB;

        try {
            $course = $DB->get_record('course', array('idnumber' => $lesson->idnumber));

            if (!$course) {

                create_course($lesson);
                $msg = SyncDB::SAVE;
            } else {

                $lesson->id = $course->id;
                update_course($lesson);
                $msg = SyncDB::EDIT;
            }
            return $msg;
        } catch (Exception $e) {
            return $e;
        }

    }

}
