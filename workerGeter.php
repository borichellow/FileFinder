<?php
class workerGeter
{
    private function file_get_contents_curl($url) {
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL,$url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
        $query = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $query;
    } 

	public function workerGetData($id, $folder){
		$url = "http://www.shutterstock.com/pic.mhtml?id=".$id."&src=id";
        $page = $this->file_get_contents_curl($url);
        preg_match_all('/<h1>.*/', $page, $title);
        $title = preg_replace("/<h1>/", "", $title[0]);
        
        preg_match_all('/id="kw_\d+".*<\/a>/', $page, $KW);
        foreach ($KW[0] as $value) {
            $arr[] = preg_replace(array('/.*html">/', '/<\/a>/'), "", $value);
        }
        $keyWords = implode(", ", $arr);

        $data = array('title' => $title[0], 'keywords' => $keyWords);
        $data = json_encode($data);
        file_put_contents("./".$folder."/Data/".$id.".txt", $data);
	}	
}
$worker = new workerGeter();
$worker->workerGetData($argv[1], $argv[2]);