<?php 
class CashFiles
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

    private function waiter($results){
        echo "Waiting fot workers...Cash for file\n";
        do{
            $flag = "yes";
            foreach ($results as $file) {
                if(!(file_exists($file))){
                    $flag = "no";
                    break;
                }
            }
        }while($flag == "no");
    }

	/*
		in: $arrayShutter: array('id' => 'ID on Shutter', 'thumb' => 'Thumb on Shutter')
		result:  $arrayShutter: array('id' => 'ID on Shutter', 'thumb' => 'adress of file')
	*/
	public function CashShutter($arrayShutter, $folder ){
		$arrayShutterNew = array();
		$i = 0;
		while ($i < count($arrayShutter)){
			$results = array();
			for ($ii = 0; $ii < 200; $ii++){
				$file = $arrayShutter[$i];
				$adress = './' . $folder . '/ShutterFiles/' . $file['id'] . '.jpg';
				$results[] = $adress;
				exec("php workerCashFiles.php ".$file['thumb']." ".$adress." >> /dev/null &");
				$arrayShutterNew[] = array('id' => $file['id'], 'thumb' => $adress);
				$i++;
                if($i == count($arrayShutter)){break;}
			}
			$this->waiter($results);
		}
		return $arrayShutterNew;
	}

	public function CashDeposit($arrayDeposit, $folder){
		$arrayDepositNew = array();
		foreach ($arrayDeposit as $id => $file) {
			$adress = './' . $folder . '/DepositFiles/' . $id . '.jpg';
			imagejpeg($this->createImage($file) , $adress);
			$arrayDepositNew[$id] = $adress;
		}
		return $arrayDepositNew;
	}
}