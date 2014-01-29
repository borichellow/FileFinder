<?php 
class GetDataFromShutter
{
	/*
    $Thumb: id of file on Shutter
    $data: array('title' => 'Title of file', 'keywords' => 'KeyWords of file')
*/

    public function GetFileData($ID){
        $url = "http://www.shutterstock.com/pic.mhtml?id=".$ID."&src=id";
        $page = file_get_contents($url);
        preg_match_all('/<h1>.*/', $page, $title);
        $title = preg_replace("/<h1>/", "", $title[0]);
        
        preg_match_all('/id="kw_\d+".*<\/a>/', $page, $KW);
        foreach ($KW[0] as $value) {
            $arr[] = preg_replace(array('/.*html">/', '/<\/a>/'), "", $value);
        }
        $keyWords = implode(", ", $arr);

        $data = array('title' => $title[0], 'keywords' => $keyWords);
        return $data;
    }

}