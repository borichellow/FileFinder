<?php 
class ShutterstockFiles
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

	private function Waiter($results, $folder){
        echo "Waiting fot workers...Shutter Portfolio\n";
        do{
            $flag = "yes";
            foreach ($results as $page) {
                if(!(file_exists("./".$folder."/ShutterFiles/".$page.".txt"))){
                    $flag = "no";
                    break;
                }
            }
        }while($flag == "no");
    }

	private function UniUrl($url){
		$urlNew = preg_replace("/.\.html/" , "", $url);
		return $urlNew;

	}

	private function GetCountOfFiles($portfolio){
		$portfolio = $this->file_get_contents_curl($portfolio);
        preg_match_all('/id="pf_num_images">.*<\/li>/', $portfolio, $count1);
        preg_match('/\d+/', $count1[0][0], $count);
        $count = (int)$count[0];
        return $count;
    }

    private function ParseAll($results, $folder){
    	echo "Parsing files...Shutter Portfolio\n";
    	$array = array();
    	foreach ($results as $page) {
   			$file = json_decode(file_get_contents("./".$folder."/ShutterFiles/".$page.".txt"), true);
   			foreach ($file as $value) {
   				$array[] = $value;
   			}
       	}
       	return $array;
    }

    private function WorkersTime($PortfolioURL, $countFiles, $folder){
    	$i = 1 ; 
    	$arrayShutter = array();
    	$countPages = (int)($countFiles/100)+1;
    	while ($i <= $countPages) {
    		$results = array();
    		for ($ii = 0; $ii < 300; $ii++){
    			$results[]= $i;
    			exec("php workerShutterFiles.php ".$PortfolioURL.$i.".html ".$i." ".$folder." >> /dev/null &");
    			$i++;
    			if($i > $countPages){break;}
    		}
    		$this->Waiter($results, $folder);
    		$arrayShutter = array_merge($arrayShutter , $this->ParseAll($results, $folder));
    	}
    	exec("rm ./".$folder."/ShutterFiles/*.txt");
    	return $arrayShutter;
    }

    private function NormalizationShutterArray($arrayShutter){
        $array = array();
        foreach ($arrayShutter as $value) {
            preg_match("/-\d+\.jpg/", $value, $arr);
            $key = preg_replace(array("/-/" , "/\.jpg/"), "", $arr[0]);
            $array[] = array("thumb" => $value, "id" => $key);
        }
        return $array;
    }

    public function ShutterFiles($PortfolioURL, $folder){
    	$countFiles = $this->GetCountOfFiles($PortfolioURL);
    	$PortfolioURL = $this->UniUrl($PortfolioURL);
    	$arrayShutter = $this->WorkersTime($PortfolioURL, $countFiles, $folder);
    	$arrayShutter = $this->NormalizationShutterArray($arrayShutter);
    	return $arrayShutter;
    }

}

// $script = new script();
// var_dump($script->ShutterFiles("http://www.shutterstock.com/gallery-176509p1.html"));