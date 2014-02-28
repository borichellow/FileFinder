<?php

class FindeImagesFork
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
    
    public function Comparator($imageD, $image2){
        //$r = rand(111, 999);
        //$imageD = imagecreatefromjpeg($depositItemThumbURL);
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
                    if ($count/($arrayX[1]*$arrayY[1]*$square) < 0.7){$count = 0;  break;}
                    $ii ++;
                }while ($ii < 4);
                $i ++;
                if ($count == 0){break;}
            }while ($i < 4);
            //var_dump($count/($xD*  $yD));
            return $koef = $count/($xD*  $yD);
        } else {return 0;}
    }

    private function AnalizWorkers($keyD , $folder){
        if(file_get_contents("./".$folder."/Results/flag.txt") == "found"){
            $file = file_get_contents("./".$folder."/Results/result.txt");
            $array = array('deposit' => $keyD, 'shutter' => (int)$file);
        }else{$array = "not found";}
        exec("rm -f ./".$folder."/Results/*.txt");
        return $array;
    }

    private function worker($depositFile, $shutterFile, $folder){
        $compare = $this->Comparator($depositFile, $shutterFile['thumb']);
        if($compare > 0.85){
            file_put_contents("./".$folder."/Results/flag.txt", "found");
            file_put_contents("./".$folder."/Results/result.txt", $shutterFile['id']);
        }
    }

    private function ForkingCompare($imageD, $arrayShutter, $folder){
        $worker_processes = 4;
        $child_processes = array();
        file_put_contents("./".$folder."/Results/flag.txt", "");
        file_put_contents("./".$folder."/Results/result.txt", "");

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
                while (($files*$worker_processes+$i) < $count &&
                 file_get_contents("./".$folder."/Results/flag.txt") != "found") {
                    $this->worker($imageD, $arrayShutter[($files*$worker_processes+$i)], $folder);
                    $files++;
                }
                exit(0);
            }
        }
    }
/*  
    $arrayDeposit: 'fileID' => 'URL to thumb' (that should be found) 
    $arrayShutter: array('URL to thumb') (that belong to this author on shutter)
    return: IDsArray: 'depositID' => 'shutterID'
*/
    public function FindeImage($arrayDeposit, $arrayShutter, $folder){
        $IDsArray = array();
        foreach ($arrayDeposit as $keyD => $valueD) {
            $time = microtime(true);
            $imageD = imagecreatefromjpeg($valueD);
            if($imageD){
                $this->ForkingCompare($imageD, $arrayShutter, $folder);
                $result = $this->AnalizWorkers($keyD, $folder);
                if (is_array($result)){
                    $IDsArray[] = $result;
                }
            }
            print "one file worked! time: ". (microtime(true) - $time). " sec \n";
        }
        //var_dump($IDsArray);
        print count($IDsArray)." files were found!\n";
        return $IDsArray;
    }
}