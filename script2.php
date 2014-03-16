<?php
class script2
{
	

    public function GetNormalURL($file){
        return exif_read_data($file);
    }

	

}
$script = new script2();
var_dump($script->GetNormalURL("http://static8.depositphotos.com/1004999/952/i/170/depositphotos_9522100-Wonderland.jpg"));