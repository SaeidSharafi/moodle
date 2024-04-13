<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 1/28/2021
 * Time: 7:20 PM
 */

namespace Synchronizer\Classes;


class Course
{
    public $idnumber = "";
    public $fullname = "";
    public $shortname = "";
    public $format = "remuiformat";
    public $categoryData ="";
    public $category ="";
    public $numsections = 5;
    public $showgrades = 1;
    public $newsitems = 0;
    public $summary_editor = array(
        'text' => '',
        'format' => 1
    );

    public function __construct($idnumber, $fullname, $shortname,$category)
    {
        $this->idnumber = $idnumber;
        $this->fullname = $fullname;
        $this->shortname = $shortname;
        $this->categoryData = $category;
    }
}
