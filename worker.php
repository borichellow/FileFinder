<?php 

    function createImage($imgURL){
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

    function SharePixel($pixel){
		$r = ($pixel >> 16) & 0xFF;
        $g = ($pixel >> 8) & 0xFF;
        $b = $pixel & 0xFF;
        if ($r == 0){$r = 1; }
        if ($g == 0){$g = 1; }
        if ($b == 0){$b = 1; }
        return array('r' => $r, 'g' => $g, 'b' => $b);
    }

    function GetPixelCount($rgbD, $rgbS){
        $count = (255 - abs($rgbD-$rgbS))/255;
        return $count;
    }

    function PixelCompare($pixelD , $pixelS){
        $rgbD = SharePixel($pixelD);
        $rgbS = SharePixel($pixelS);
        $count = array();
        foreach ($rgbD as $key => $value) {
            $count[$key] = GetPixelCount($value, $rgbS[$key]);
        }
        return $count['r']*$count['g']*$count['b'];
    }

    function NormalizationShutterFile($shutterstockItemThumbURL, $x, $y){
        //$image2 = imagecreatefromjpeg($shutterstockItemThumbURL);
        $image2 = createImage($shutterstockItemThumbURL);
        if (is_string($image2)){
            return $image2;
        }else{
            if ($x > $y){
                $image1 = imagecreatetruecolor((imagesx($image2)), (imagesy($image2)-17));}
            else {$image1 = imagecreatetruecolor((imagesx($image2)-17), (imagesy($image2)));}
            imagecopymerge($image1, $image2, 0, 0, 0, 0, imagesx($image1), imagesy($image1), 100);
            $imageS = imagecreatetruecolor($x, $y);
            imagecopyresampled($imageS, $image1, 0, 0, 0, 0, imagesx($imageS), imagesy($imageS), 
                               imagesx($image1), imagesy($image1));
            return $imageS;
        }
    }
    
    function Comparator($depositItemThumbURL, $image2){
        //$r = rand(111, 999);
        //$imageD = imagecreatefromjpeg($depositItemThumbURL);
        $imageD = createImage($depositItemThumbURL);
        if (is_string($imageD)){
            return $imageD;
        }else{
            //imagejpeg($imageD , './input_' . $r . '.jpg');
            $xD = imagesx($imageD);
            $yD = imagesy($imageD);
            $imageS = NormalizationShutterFile($image2, $xD, $yD);
            if (is_string($imageS)){
                return $imageS;
            }else{
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
                                    $count = $count + PixelCompare($pixelD, $pixelS);
                                }
                            }
                            $square ++;
                            if ($count/($arrayX[1]*$arrayY[1]*$square) <0.7){$count = 0;  break;}
                            $ii ++;
                        }while ($ii < 4);
                        $i ++;
                        if ($count == 0){break;}
                    }while ($i < 4);
                    //var_dump($count/($xD*  $yD));
                    return $koef = $count/($xD*  $yD);
                } else {return 0;}
            }
        }
    }

    function hardWork($depositThumbURL, $shutterstockThumbURL, $shutterID, $folder){
    	$result = Comparator($depositThumbURL, $shutterstockThumbURL);
    	file_put_contents("./".$folder."/Results/".$shutterID.".txt", $result);
        var_dump($result);
    }
	

 
hardWork($argv[1], $argv[2], $argv[3], $argv[4]);

//$t = microtime(true);
// $worker->hardWork("http://static8.depositphotos.com/1000270/1042/i/170/depositphotos_10428181-Red-wine-bottle-and-fruit-with-glass.jpg", 
//     "http://thumb7.shutterstock.com/photos/thumb_large/138433/101706925.jpg", 
//     "81265270", "folde");
//echo microtime(true) - $t;
