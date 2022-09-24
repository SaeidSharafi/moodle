<?php




function login()
{
    $data = array(
            'username' => 'TabrizLms',
            'password' => 'tabriz123'
    );

    $params = http_build_query($data, null, "&");
    $url = 'http://amozesh.tabrizu.ac.ir/samawebservices/services/EducationService.svc/web/GetOfferedTermLesson?' . $params;


    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    $info = curl_getinfo($ch);
    $cookies = array();
    preg_match_all('/Set-Cookie:(?<cookie>\s{0,}.*)$/im', $result, $cookies);

    // var_dump(http_parse_cookie($cookies['cookie']));
    //var_dump($cookies['cookie']);
    //$parse_coockies = $this->parse_cookies($cookies['cookie'][0]);
    $cookieParts = array();
    //preg_match_all('/Set-Cookie:\s{0,}(?P<name>[^=]*)=(?P<value>[^;]*).*?/im', $result, $cookieParts);

    curl_close($ch);
    //$this->auth = $parse_coockies[0];
    echo '<pre>';
    var_dump($info);
    echo '</pre>';
    var_dump($result);

    return $result;
}
login();