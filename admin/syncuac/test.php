<?php



require_once('../../config.php');
include_once "classes/Handler.php";
include_once "classes/utils.php";
function login()
{
    global $CFG;
    if (!$CFG->samauser || !$CFG->samapass || !$CFG->samaurl){
        echo 'لطفا فیلدهای مربوط به سامانه را در فایل config.php وارد کنید'. '<br>';
        echo '$CFG->samaurl : ' . ($CFG->samaurl ? 'تنظیم شده' : 'تنظیم نشده') .'<br>';
        echo '$CFG->samauser : ' . ($CFG->samauser ? 'تنظیم شده' : 'تنظیم نشده') .'<br>';
        echo '$CFG->samapass : ' . ($CFG->pass ? 'تنظیم شده' : 'تنظیم نشده') .'<br>';
        return;
    }
    //$data = array(
    //        'username' => $CFG->samauser,
    //        'password' => $CFG->samapass
    //);
    //
    //$params = http_build_query($data, null, "&");
    //$url = $CFG->samaurl.'/services/AuthenticationService.svc/web/Login?' . $params;
    //
    //
    //$ch = curl_init($url);
    //curl_setopt($ch, CURLOPT_HEADER, 1);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //
    //$result = curl_exec($ch);
    //$info = curl_getinfo($ch);
    //$cookies = array();
    //preg_match_all('/Set-Cookie:(?<cookie>\s{0,}.*)$/im', $result, $cookies);
    //
    //// var_dump(http_parse_cookie($cookies['cookie']));
    ////var_dump($cookies['cookie']);
    ////$parse_coockies = $this->parse_cookies($cookies['cookie'][0]);
    //$cookieParts = array();
    ////preg_match_all('/Set-Cookie:\s{0,}(?P<name>[^=]*)=(?P<value>[^;]*).*?/im', $result, $cookieParts);
    //
    //curl_close($ch);
    ////$this->auth = $parse_coockies[0];
    //
    //echo '<pre>';
    //var_dump($info);
    //echo '</pre>';
    //var_dump($result);
    $handler = new \Synchronizer\Classes\Handler();
    $result = $handler->getLessonTeachers('14011','1','1');
    echo '<pre>';
    var_dump($result);
    echo '</pre>';

    return $result;
}
login();
