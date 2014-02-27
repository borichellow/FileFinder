<?php
class workerCashFiles
{
	private function createImage($imgURL){
        for($i = 0; $i < 10; $i++){
            $img = imagecreatefromjpeg($imgURL);
            $i++;
            if ($img){
                break;
            }
            sleep(0.5);
        }
        if (!($img)){
            return "!!! Some image can not be downloaded !!!\n!!! CHECK CONNOCTION TO INTERNET !!!";
        }else{
            return $img;
        }
    }

	public function Cash($file, $adress){
		imagejpeg($this->createImage($file) , $adress);
	}
}
$worker = new workerCashFiles();
$worker->Cash($argv[1], $argv[2]);