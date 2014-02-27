<?php
class ConvertRezult
{
	private function arrayToXml($data, &$xmlData) {
	    foreach($data as $key => $value) {
	        if(is_array($value)) {
	            $subnode = $xmlData->addChild("$key");
	            $this->arrayToXml($value, $subnode);   
	        }
	        else {
	            $xmlData->addChild("$key","$value");
	        }
	    }
	}

	public function convertToXML($data){
		$xmlData = new SimpleXMLElement("<?xml version=\"1.0\"?><data></data>");
		$this->arrayToXml($data, $xmlData);
		return $xmlData;
	}	

	private function FindThumbByID($AllFilesShuter, $id){
		foreach ($AllFilesShuter as $value) {
			if($value['id'] == $id){
				return $value['thumb'];
				break;
			}
		}
	}

	private function NormalizationOfDataArray($data, $AllFilesShuter){
		foreach ($data as $value) {
			$value['shutterstock_thumb'] = $this->FindThumbByID($AllFilesShuter , $value['shutterstock_item_id']);
		}		
		return $data;
	}

/*
		IN: $data:  array('item_id' , 'title', 'keywords', 'shutterstock_item_id')
		OUT: json($data)
*/
	public function convertToJson($data, $folder, $AllFilesShuter){
		$data = $this->NormalizationOfDataArray($data, $AllFilesShuter);
		$data = json_encode(array('items' => $data));
		file_put_contents("./resultFor".$folder.".txt", $data);
		return $data;
	}
}
// $converter = new ConverToXml();
// var_dump($converter->convert(array(array('ddd'=>'val1', 'ccc'=>'val2', 'eee'=>'val3'),
// 								array('ddd'=>'val11', 'ccc'=>'val22', 'eee'=>'val33'),
// 								array('ddd'=>'val111', 'ccc'=>'val222', 'eee'=>'val333'),
// 								array('ddd'=>'val1111', 'ccc'=>'val2222', 'eee'=>'val3333')
// 								)));