<?php
class ConvertToXml
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

	public function convert($data){
		$xmlData = new SimpleXMLElement("<?xml version=\"1.0\"?><data></data>");
		$this->arrayToXml($data, $xmlData);
		return $xmlData;
	}	
}
// $converter = new ConverToXml();
// var_dump($converter->convert(array(array('ddd'=>'val1', 'ccc'=>'val2', 'eee'=>'val3'),
// 								array('ddd'=>'val11', 'ccc'=>'val22', 'eee'=>'val33'),
// 								array('ddd'=>'val111', 'ccc'=>'val222', 'eee'=>'val333'),
// 								array('ddd'=>'val1111', 'ccc'=>'val2222', 'eee'=>'val3333')
// 								)));