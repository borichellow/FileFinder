<?php 
class worker
{
	public function SharePixel($pixel){
        $r = ($pixel >> 16) & 0xFF;
        $g = ($pixel >> 8) & 0xFF;
        $b = $pixel & 0xFF;
        if ($r == 0){$r = 1; }
        if ($g == 0){$g = 1; }
        if ($b == 0){$b = 1; }
        return array('r' => $r, 'g' => $g, 'b' => $b);
    }

    public function GetPixelCount($rgbD, $rgbS){
        $count = (255 - abs($rgbD-$rgbS))/255;
        return $count;
    }

    public function PixelCompare($pixelD , $pixelS){
        $rgbD = $this->SharePixel($pixelD);
        $rgbS = $this->SharePixel($pixelS);
        $count = array();
        foreach ($rgbD as $key => $value) {
            $count[$key] = $this->GetPixelCount($value, $rgbS[$key]);
        }
        return $count['r']*$count['g']*$count['b'];
    }

    public function NormalizationShutterFile($shutterstockItemThumbURL, $x, $y){
        $image2 = imagecreatefromjpeg($shutterstockItemThumbURL);
        if ($x > $y){
            $image1 = imagecreatetruecolor((imagesx($image2)), (imagesy($image2)-17));}
        else {$image1 = imagecreatetruecolor((imagesx($image2)-17), (imagesy($image2)));}
        imagecopymerge($image1, $image2, 0, 0, 0, 0, imagesx($image1), imagesy($image1), 100);
        $imageS = imagecreatetruecolor($x, $y);
        imagecopyresampled($imageS, $image1, 0, 0, 0, 0, imagesx($imageS), imagesy($imageS), imagesx($image1), imagesy($image1));
        return $imageS;
    }
    
    public function Comparator($depositItemThumbURL, $image2){
        //$r = rand(111, 999);
        $imageD = imagecreatefromjpeg($depositItemThumbURL);
        //imagejpeg($imageD , './input_' . $r . '.jpg');
        $xD = imagesx($imageD);
        $yD = imagesy($imageD);
        $imageS = $this->NormalizationShutterFile($image2, $xD, $yD);
        $xS = imagesx($imageS);
        $yS = imagesy($imageS);
        if ($xD*$yS/($xS*$yD) > 0.9 
            && $xD*$yS/($xS*$yD) < 1.1){
            //imagejpeg($imageS , "C:\img/result_" . $r . '.jpg');
            $count = 0;
            if ($yD > $yS){$maxY = $yS;}
            else {$maxY = $yD;}
            $maxX = $xD;
            $arrayX = array(0, intval($maxX/4), intval($maxX/2), intval($maxX*3/4), $maxX);
            $arrayY = array(0, intval($maxY/4), intval($maxY/2), intval($maxY*3/4), $maxY);
            $i=0;
            $square = 0;
            do{
                $ii = 0;
                do{
                    for($y = $arrayY[$i]; $y <  $arrayY[($i +1)]; $y ++){
                        for($x = $arrayX[$ii]; $x < $arrayX[($ii +1)]; $x ++){
                            $pixelD = imagecolorat($imageD, $x, $y);
                            $pixelS = imagecolorat($imageS, $x, $y);
                            $count = $count + $this->PixelCompare($pixelD, $pixelS);
                        }
                    }
                    $square ++;
                    if ($count/($arrayX[1]*$arrayY[1]*$square) <0.5){$count = 0;  break;}
                    $ii ++;
                }while ($ii < 4);
                $i ++;
                if ($count == 0){break;}
            }while ($i < 4);
            //var_dump($count/($xD*  $yD));
            return $koef = $count/($xD*  $yD);
        } else {return 0;}
    }

    public function hardWork($depositThumbURL, $shutterstockThumbURL, $shutterID){
    	//$time = microtime(true);
    	$result = $this->Comparator($depositThumbURL, $shutterstockThumbURL);
    	file_put_contents("./Results/".$shutterID.".txt", $result);
		//var_dump(microtime(true) - $time);
    }
	
}
$worker = new worker();
$worker->hardWork($argv[1], $argv[2], $argv[3]);
// var_dump($worker->hardWork("http://static6.depositphotos.com/1006472/614/i/170/depositphotos_6149688-Drunk-man.jpg", 
// 	"http://thumb7.shutterstock.com/thumb_large/191161/191161,1311093697,1/stock-photo-drunk-man-at-the-pub-table-with-a-glass-of-beer-81265270.jpg", 
// 	"81265270"));

