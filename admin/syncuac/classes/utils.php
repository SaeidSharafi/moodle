<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 10/28/2020
 * Time: 1:43 PM
 */

namespace Synchronizer\Classes;

class TextType
{

    const ERROR = 1;
    const SUCCESS = 2;
    const INFO = 3;
    const PRIMARY = 4;


}
class Status
{
    const SUCCESS = 1;
    const ERROR = -1;
    const FATAL_ERROR = -2;
    const NEXT = 2;
    const END = 3;
    const SAVE = 11;
    const EDIT = 12;
    const SKIP = 13;

}

function logit($text){
    $date = date('Y-m-d');
    $log_path = __DIR__.'/../logs/'.$date.".log";
    $log_file = fopen($log_path, 'ab');
    $time = date('Y-m-d H:i:s');
    $text = "{$time}, [info] : $text \n";
    fwrite($log_file, $text);
    fclose($log_file);
    return $log_path;
}
