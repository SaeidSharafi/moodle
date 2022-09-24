<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 1/28/2021
 * Time: 7:35 PM
 */

namespace Synchronizer\Classes;


class Category
{

    public $facultyCode ="";
    public $facultyTitle ="";
    public $lessonGroupId ="";
    public $groupTitle = "";

    public function __construct($facultyCode, $facultyTitle, $lessonGroupId,$groupTitle)
    {
        $this->facultyCode = $facultyCode;
        $this->facultyTitle = $facultyTitle;
        $this->lessonGroupId = $lessonGroupId;
        $this->groupTitle = $groupTitle;

    }
}