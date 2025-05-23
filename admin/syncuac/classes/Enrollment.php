<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 1/28/2021
 * Time: 7:21 PM
 */

namespace Synchronizer\Classes;


class Enrollment
{
    public $courseid = "";
    public $coursename = "";
    public $username = "";
    public $roleid = 5;
    public $roletext = "";
    public $term ="";
    public $enroldate ="";
    public $is_updated = 1;


    public function __construct($courseid, $username, $roleid,$term,$is_updated= 1)
    {
        $this->courseid = $courseid;
        $this->username = $username;
        $this->roleid = $roleid;
        $this->term = $term;
        $this->is_updated = $is_updated;
        $this->enroldate = strtotime("now");
    }
}