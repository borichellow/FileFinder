<?php 
class CashFilesFork
{

	private function NewShutterArray($arrayShutter, $folder){
		$arrayShutterNew = array();
		foreach ($arrayShutter as $file) {
			$arrayShutterNew[] = array('id' => $file['id'], 'thumb' => './' . $folder . '/ShutterFiles/' . $file['id'] . '.jpg');
		}
		return $arrayShutterNew;
	}

    private function waiter($arrayShutter){
        echo "Waiting fot workers...Cash for file\n";
        do{
            $flag = "yes";
            foreach ($arrayShutter as $file) {
                if(!(file_exists($file['thumb']))){
                    $flag = "no";
                    break;
                }
            }
            sleep(0.5);
        }while($flag == "no");
    }

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

    private function workerCash($file, $adress){
    	imagejpeg($this->createImage($file) , $adress);
    }

    private function ForkingCash($arrayShutter, $folder){
        $worker_processes = 5;
        $child_processes = array();

        for ($i = 0; $i < $worker_processes; $i++) {

            $child_pid = pcntl_fork();
            
            if ($child_pid == -1) {
                die ("Can't fork process");
            } elseif ($child_pid) {
                //print "Parent, created child: $child_pid\n";
                $child_processes[] = $child_pid;     
            
                // В данный момент все процессы отфоркнуты, можно начать ожидание
                if ($i == ( $worker_processes -1 ) ) {
                    foreach ($child_processes as $process_pid) {
                        // Ждем завершение заданного дочернего процесса
                        $status = 0;
                        pcntl_waitpid($process_pid, $status); 
                    }
                }
            } else {
                $files = 0;
                $count = count($arrayShutter);
                do{
                	$index = $files*$worker_processes+$i;
                	$adress = './' . $folder . '/ShutterFiles/' . $arrayShutter[$index]['id'] . '.jpg';
                    $this->workerCash($arrayShutter[$index]['thumb'], $adress);
                    $files++;
                }while ($index < ($count-$worker_processes));
                exit(0);
            }
        }
    }

	/*
		in: $arrayShutter: array('id' => 'ID on Shutter', 'thumb' => 'Thumb on Shutter')
		result:  $arrayShutter: array('id' => 'ID on Shutter', 'thumb' => 'adress of file')
	*/
	public function CashShutter($arrayShutter, $folder ){
		$this->ForkingCash($arrayShutter, $folder);
		$arrayShutterNew = $this->NewShutterArray($arrayShutter, $folder);
		$this->waiter($arrayShutterNew);
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