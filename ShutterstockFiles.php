<?php
class ShutterstockFiles
{
	public function GetCountOfFiles($portfolio){
        preg_match_all('/id="pf_num_images">.*<\/li>/', $portfolio, $count1);
        preg_match('/\d+/', $count1[0][0], $count);
        $count = (int)$count[0];
        return $count;
    }

    public function FillingArrayShutter($arrayShutter, $portfolio){
        preg_match_all('/<img src="http:\/\/thumb.*\.jpg/', $portfolio, $arr);
        foreach ($arr[0] as $value) {
            $arrayShutter[] = preg_replace('/<img src="/', "", $value);
        }
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

    public function NextPage($page){
        preg_match_all('/.*class="grid_pager_button_next"/', $page, $nextpage);
        if (isset($nextpage[0][0])){
            $nextpageURL = preg_replace(array("/.*href='/","/'.* class.*/"), "", $nextpage[0][0]);
            return "http://www.shutterstock.com".$nextpageURL;
        }else{
            return "end!";
        }
    }

/* 
    $PortfolioURL: url of authirs`s portfolio on Shutter
    $arrayShutter: array('id' => 'ID on Shutter', 'thumb' => 'Thumb on Shutter')
*/
    public function ShutterFiles($PortfolioURL){
        //$time = microtime(true);
        $arrayShutter = array();
        do {
            $portfolio = file_get_contents($PortfolioURL);
            $arrayShutter = $this->FillingArrayShutter($arrayShutter, $portfolio);
            $PortfolioURL = $this->NextPage($portfolio);
        }while ($PortfolioURL != "end!");
        //var_dump(microtime(true) - $time);
        $arrayShutter = $this->NormalizationShutterArray($arrayShutter);
        //var_dump(microtime(true) - $time);

        return $arrayShutter;     
        //return count($arrayShutter);
    }
}

 // $com = new ShutterstockFiles();
 // var_dump($com->ShutterFiles("http://www.shutterstock.com/gallery-844213p1.html"));