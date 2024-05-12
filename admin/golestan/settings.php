<?php

/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 10/30/2020
 * Time: 1:07 PM
 */

include_once "../../config.php";
class Config
{

    /** @var string Secret key to use for security */
    public static $security_key = "x";
    public static $db_server = 'localhost';
    public static $db_user = 'moodleuser';
    public static $db_pass = 'password';
    public static $db_name = 'pafc_dev';

    /** @var bool set to false if you are using mysql */
    public static $mssql = false;

    /** @var string not used */
    public static $mssql_prefix = "dbo";


    public static $soap_pass='100100';

    public static $user_table='user';
    public static $category_table='course_categories';
    public static $course_table='course';
    public static $enrol_table='enrol_db';
    //public static $user_enrol_table='mdl_user_enrolments';

    /** @var int role id of student */
    public static $student_role_id=5;

    /** @var int role id of teacher */
    public static $teacher_role_id=3;

    /** @var bool update existing students */
    public static $update_students= true;

    /** @var bool update existing teachers */
    public static $update_teachers= true;

    /** @var bool update existing teachers */
    public static $update_courses= true;


}


