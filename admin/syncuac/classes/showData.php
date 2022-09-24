<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 2/22/2021
 * Time: 9:31 PM
 */

namespace Synchronizer\Classes;


class showData
{
    public function showEnrolments($enrolments){

        $headers = array("کد دوره","کد گروه","نام کاربری");

        foreach ($headers as $header ){
            $th[] ="<th>" . $header ."</th>";
        }

        foreach ($enrolments as $enrolment){
            $td[] = "<td>";
        }


    }
}