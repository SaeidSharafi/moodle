<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 10/28/2020
 * Time: 1:43 PM
 */


class Courses_1248
{

    const TERM = 2;
    const STATE = 4;
    const CENTER = 5;
    const COURSE_STATE = 6;
    const COLLEGE = 12;
    const GROUP = 16;
    const COURSE_NUMBER = 20;
    const COURSE_GROUP = 24;
    const COURSE_DEGREE = 28;
    const COURSE_CAPACITY = 26;
    const UNIT = 28;


    const PRI_PREREQUISITES_UQID = 258;
    const PRI_PREREQUISITES_ID = 1;
    const PRI_COURSE_LIST_UQID = 104;
    const PRI_COURSE_LIST_ID = 18;

}
class Enrollment_1171
{

    const SOURCE = 2;
    const STD_SOURCE = 3;
    const STD_ID = 4;
    const TERM = 16;
    const CENTER = 41;
    const COURSE_NUMBER = 20;
    const COURSE_GROUP = 24;
    const COURSE_DEGREE = 28;


    const PRI_LETTER_UQID = 245;
    const PRI_LETTER_ID = 2;
    const PRI_TERM_UQID = 10;
    const PRI_TERM_ID = 4;

}
class Students_1132
{

    const SOURCE = 1;
    const STD_SOURCE = 2;
    const STD_ID = 4;
    const TERM = 16;
    const CENTER = 41;


    const PRI_LETTER_UQID = 245;
    const PRI_LETTER_ID = 5;
    const PRI_TERM_UQID = 10;
    const PRI_TERM_ID = 10;

}
class Teachers_1131
{

    const CENTER = 1;
    const TCH_ID = 2;
    const TCH_COLLEGE = 4;



    const PRI_LETTER_UQID = 17145;
    const PRI_LETTER_ID = 2;


}
function create_pub($id, $from="", $to="")
{
    $pub = "<N id='" . $id .
        "' F1='" . $from .
        "' T1='" . $to .
        "' F2='' T2='' />";
    return $pub;
}

function create_pri($uqid, $id, $from="", $to="")
{
    $pri = "<N UQID='" . $uqid .
        "' id='" . $id .
        "' F='" . $from .
        "' T='" . $to .
        "' />";
    return $pri;
}