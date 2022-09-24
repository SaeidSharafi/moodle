<?php

/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 10/30/2020
 * Time: 1:07 PM
 */
namespace Synchronizer;
@ini_set('display_errors', 0);

class Settings

{
    /** @var string Secret key to use for security */
    public static $security_key = "KZwPUs45L6ddeK6yLLR24pmRYuUmAqYF";


    /** @var bool set to false if you are using mysql */
    public static $mssql = true;

    /** @var string not used */
    public static $mssql_prefix = "dbo";


    public static $sec_key = "KZwPUs45L6ddeK6yLLR24pmRYuUmAqYF";

    public static $enrol_table='enrol_db';
    //public static $user_enrol_table='mdl_user_enrolments';

    /** @var int role id of student */
    public static $student_role_id=5;

    /** @var int role id of teacher */
    public static $teacher_role_id=3;

    /** @var bool update existing students */
    public static $update_students= false;

    /** @var bool update existing teachers */
    public static $update_teachers= false;

    /** @var bool update existing teachers */
    public static $update_courses= true;

    public static $study_levels = array(5,6,7,8);


    public static $update_password = false;

}


