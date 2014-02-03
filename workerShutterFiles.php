<?php 
class workerShutterFiles
{
	public function FillingArrayShutter($url, $i){
		$arrayShutter = array();
		$page = file_get_contents($url);
        preg_match_all('/<img src="http:\/\/thumb.*\.jpg/', $page, $arr);
        foreach ($arr[0] as $value) {
            $arrayShutter[] = preg_replace('/<img src="/', "", $value);
        }
        $json = json_encode($arrayShutter);
        file_put_contents("./ShutterFiles/".$i.".txt", $json);
    }
}
$worker = new workerShutterFiles();
$worker->FillingArrayShutter($argv[1], $argv[2]);
//$worker->FillingArrayShutter("http://www.shutterstock.com/gallery-176509p3.html", 3);