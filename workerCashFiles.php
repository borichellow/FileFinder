<?php

function file_get_contents_curl($url) {
    $curl_handle=curl_init();
    curl_setopt($curl_handle, CURLOPT_URL,$url);
    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
    $query = curl_exec($curl_handle);
    curl_close($curl_handle);
    return $query;
} 

function createImage($imgURL){
    for($i = 0; $i < 60; $i++){
        $img = file_get_contents_curl($imgURL);
        if ($img){
            break;
        }else{ 
            $img = false;
        }
        sleep(0.5);
    }
    return $img;
}

function Cash($file, $adress){
    $image = createImage($file);
    if(!($image)){
        file_put_contents("./error.txt", "cant cash file: ".$adress."\n", FILE_APPEND);
    }else{
        imagejpeg(imagecreatefromstring($image) , $adress);
    }
}

Cash($argv[1], $argv[2]);