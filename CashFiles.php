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
        for($i = 0; $i<180;$i++){
            $flag = true;
            foreach ($results as $file) {
                if(!(file_exists($file))){
                    $flag = false;
                    break;
                }
            }
            if($flag){
            	break;
            }
            sleep(1);
        }
        if(!$flag){
        	print "---------------------------------
        		\n!!! Oops something went wrong !!! 
        		\n  !!! Check the connection !!!
        		\n---------------------------------\n";
        	exit;
        }
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
				$adress = './' . $folder . '/ShutterFiles/' . $arrayShutter[$i]['id'] . '.jpg';
				$results[] = $adress;
				exec("php workerCashFiles.php ".$arrayShutter[$i]['thumb']." ".$adress." >> /dev/null &");
				$arrayShutterNew[] = array('id' => $arrayShutter[$i]['id'], 'thumb' => $adress);
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