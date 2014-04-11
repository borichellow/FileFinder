<?php 

function file_get_contents_curl($url) {
    $curl_handle=curl_init();
    curl_setopt($curl_handle, CURLOPT_URL,$url);
    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
    $query = curl_exec($curl_handle);
    curl_close($curl_handle);
    return $query;
} 

function FillingArrayShutter($url, $i, $folder){
	$arrayShutter = array();
	$page = file_get_contents_curl($url."?sort_method=newest&safesearch=1");
    preg_match_all('/<img src="http:\/\/thumb.*\.jpg/', $page, $arr);
    foreach ($arr[0] as $value) {
        $arrayShutter[] = preg_replace('/<img src="/', "", $value);
    }
    $json = json_encode($arrayShutter);
    file_put_contents("./".$folder."/ShutterFiles/".$i.".txt", $json);
}


FillingArrayShutter($argv[1], $argv[2], $argv[3]);
//$worker->FillingArrayShutter("http://www.shutterstock.com/gallery-176509p3.html", 3);