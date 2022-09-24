<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 1/28/2021
 * Time: 4:20 PM
 */
namespace Synchronizer\Classes;

class Student
{

    public $firstname = "";
    public $lastname = "";
    public $code = "";
    public $national_code = "";
    public $study_level = "";
    public $auth = 'manual';
    public $confirmed = 1;
    public $deleted = 0;
    public $lang = 'fa';
    public $country = 'IR';
    public $descriptionformat = 1;
    public $ajax = 0;
    public $mnethostid;
    public $username;
    public $password;
    public $email;
    public $city = 'تهران';

    public function __construct($first_name, $last_name, $username, $national_code, $study_level,$email)
    {
        $this->firstname = $first_name;
        $this->lastname = $last_name;
        $this->username = $username;
        $this->study_level = $study_level;
        $this->national_code = $national_code;
        $this->email = $email;
    }

    public function set_value($key, $value)
    {
        switch (strtolower($key)) {
            case "first_name":
                $this->firstname = $value;
                return;
            case "last_name":
                $this->lastname = $value;
                return;
            case "username":
                $this->username = $value;
                return;
            case "study_level":
                $this->study_level = $value;
                return;
        }
    }
}