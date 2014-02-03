<?php 
class CashFiles
{

	/*
		in: $arrayShutter: array('id' => 'ID on Shutter', 'thumb' => 'Thumb on Shutter')
		result:  $arrayShutter: array('id' => 'ID on Shutter', 'thumb' => 'adress of file')
	*/
	public function Cash($arrayShutter, $folder = 'ShutterFiles'){
		$arrayShutterNew = array();
		foreach ($arrayShutter as $file) {
			$adress = './' . $folder . '/' . $file['id'] . '.jpg';
			imagejpeg(imagecreatefromjpeg($file['thumb']) , $adress);
			$arrayShutterNew[] = array('id' => $file['id'], 'thumb' => $adress);
		}
		return $arrayShutterNew;
	}

	public function CashDeposit($arrayDeposit, $folder = 'DepositFiles'){
		$arrayDepositNew = array();
		foreach ($arrayDeposit as $id => $file) {
			$adress = './' . $folder . '/' . $id . '.jpg';
			imagejpeg(imagecreatefromjpeg($file) , $adress);
			$arrayDepositNew[$id] = $file;
		}
		return $arrayDepositNew;
	}
}