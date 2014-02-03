<?php 
class ShutterstockFiles
{

	public function Waiter($results){
        echo "Waiting fot workers...Shutter Portfolio\n";
        do{
            $flag = "yes";
            foreach ($results as $page) {
                if(!(file_exists("./ShutterFiles/".$page.".txt"))){
                    $flag = "no";
                    break;
                }
            }
        }while($flag == "no");
    }

	public function UniUrl($url){
		$urlNew = preg_replace("/.\.html/" , "", $url);
		return $urlNew;

	}

	public function GetCountOfFiles($portfolio){
		$portfolio = file_get_contents($portfolio);
        preg_match_all('/id="pf_num_images">.*<\/li>/', $portfolio, $count1);
        preg_match('/\d+/', $count1[0][0], $count);
        $count = (int)$count[0];
        return $count;
    }

    public function ParseAll($results){
    	echo "Parsing files...Shutter Portfolio\n";
    	$array = array();
    	foreach ($results as $page) {
   			$file = json_decode(file_get_contents("./ShutterFiles/".$page.".txt"), true);
   			foreach ($file as $value) {
   				$array[] = $value;
   			}
       	}
       	return $array;
    }

    public function WorkersTime($PortfolioURL, $countFiles){
    	$i = 1 ; 
    	$arrayShutter = array();
    	$countPages = (int)($countFiles/100)+1;
    	while ($i <= $countPages) {
    		$results = array();
    		for ($ii = 0; $ii < 300; $ii++){
    			$results[]= $i;
    			exec("php workerShutterFiles.php ".$PortfolioURL.$i.".html ".$i." >> /dev/null &");
    			$i++;
    			if($i > $countPages){break;}
    		}
    		$this->Waiter($results);
    		$arrayShutter = array_merge($arrayShutter , $this->ParseAll($results));
    	}
    	exec("rm ./ShutterFiles/*.txt");
    	return $arrayShutter;
    }

    public function NormalizationShutterArray($arrayShutter){
        $array = array();
        foreach ($arrayShutter as $value) {
            preg_match("/-\d+\.jpg/", $value, $arr);
            $key = preg_replace(array("/-/" , "/\.jpg/"), "", $arr[0]);
            $array[] = array("thumb" => $value, "id" => $key);
        }
        return $array;
    }

    public function ShutterFiles($PortfolioURL){
    	$countFiles = $this->GetCountOfFiles($PortfolioURL);
    	$PortfolioURL = $this->UniUrl($PortfolioURL);
    	$arrayShutter = $this->WorkersTime($PortfolioURL, $countFiles);
    	$arrayShutter = $this->NormalizationShutterArray($arrayShutter);
    	return $arrayShutter;
    }

}

// $script = new script();
// var_dump($script->ShutterFiles("http://www.shutterstock.com/gallery-176509p1.html"));