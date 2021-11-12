<?php
$url = "https://vaccinecard.salamat.gov.ir/63ec9ad8529061400398ac10f6410f9dac9a";
$base_url = "https://vcrservice95361.salamat.gov.ir/Api/Card/VaccineCardPe?vk=";
$parsed_url = parse_url($url);
if (array_key_exists('path',$parsed_url)){
    $path = $parsed_url['path'];
    $key = ltrim($path,'/');
    if (strlen($key) == 36){
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $base_url . $key);
            curl_setopt($ch, CURLOPT_HTTPHEADER, 1);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $info = curl_getinfo($ch);
            $obj = json_decode($response);
            var_dump($obj);
            if ($obj->Status == 0){
                if ($obj->Data->NationalID == "2050166206"){
                    echo "Success";
                }
            }else{
                echo "Not Found";
            }
            var_dump($response);
        }catch (Exception $e){
            echo $e->getTrace();
        }

    }else{
        echo "ERROR";
    }
}else{
    echo "ERROR";
}