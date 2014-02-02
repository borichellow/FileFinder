<?php

class FindeImages
{

    public function Waiter($results){
        echo "Waiting fot workers...\n";
        do{
            $flag = "yes";
            foreach ($results as $file) {
                if(!(file_exists("./Results/".$file.".txt"))){
                    $flag = "no";
                    break;
                }
            }
        }while($flag == "no");
    }

    public function resultAnaliz($results){
        $result = 0;
        foreach ($results as $file) {
            $coef = file_get_contents("./Results/".$file.".txt");
            if($coef > 0.8){
                $result = $file;
                break;
            }
        }
        return $result;
    }

    public function findShutterImage($valueD, $arrayShutter){
        $i = 0;
        print "Analiz...\n";
        while ( $i < count($arrayShutter)) { 
            $results = array();
            for ($ii=0; $ii < 200; $ii++){
                //print " ii: ". $ii;
                $results[] = $arrayShutter[$i]['id'];
                exec("php worker.php ".$valueD." ".$arrayShutter[$i]['thumb']." ".$arrayShutter[$i]['id']." >> /dev/null &");  
                $i++;
                if($i == count($arrayShutter)){break;}
            }
            $this->Waiter($results);
            //print "Analiz...\n";
            $result = $this->resultAnaliz($results);
            //print "result: ".$result. "\n";
            if ($result != 0){
                print "result: ".$result. "\n";
                break;
            }
        }
        return $result;
    }
/*  
    $arrayDeposit: 'fileID' => 'URL to thumb' (that should be found) 
    $arrayShutter: array('id' => 'ID on Shutter', 'thumb' => 'Thumb on Shutter')
    return: IDsArray: 'depositID' => 'shutterID'
*/
    public function FindeImage($arrayDeposit, $arrayShutter){
        $IDsArray = array();
        foreach ($arrayDeposit as $keyD => $valueD) {
            $id = $this->findShutterImage($valueD, $arrayShutter);
            if ($id != 0){
                $IDsArray[$keyD] = $id;
            }
            exec("rm Results/*.txt");
        }
        return $IDsArray;
    }
}



// //$find = new FindeImages();
// // var_dump($find->FindeImage(array('26995965' => 'http://st.depositphotos.com/1557418/2699/v/170/depositphotos_26995965-Vector-travel-car.jpg'),
// //                 array(array('id' => '140412220', 'thumb'=> 'http://thumb1.shutterstock.com/thumb_large/844213/140412220/stock-vector-summer-jeep-car-on-beach-with-palm-140412220.jpg'), 
// //                     array('id' => '150122339', 'thumb'=> 'http://thumb7.shutterstock.com/thumb_large/844213/150122339/stock-vector-vector-flat-icon-set-150122339.jpg'), 
// //                     array('id' => '136442432', 'thumb'=> 'http://thumb9.shutterstock.com/thumb_large/844213/136442432/stock-vector-fresh-lemon-136442432.jpg'),
// //                     array('id' => '139052585', 'thumb'=> 'http://thumb7.shutterstock.com/thumb_large/844213/139052585/stock-vector-business-briefcase-full-of-money-139052585.jpg'),
// //                     array('id' => '143055019', 'thumb'=> 'http://thumb7.shutterstock.com/thumb_large/844213/143055019/stock-vector-eco-car-143055019.jpg'),
// //                     array('id' => '126238625', 'thumb'=> 'http://thumb7.shutterstock.com/photos/thumb_large/844213/126238625.jpg'),
// //                     array('id' => '113199499', 'thumb'=> 'http://thumb7.shutterstock.com/photos/thumb_large/844213/113199499.jpg'),
// //                     array('id' => '98158652', 'thumb'=> 'http://thumb9.shutterstock.com/photos/thumb_large/844213/98158652.jpg'),
// //                     array('id' => '147708794', 'thumb'=> 'http://thumb1.shutterstock.com/photos/thumb_large/844213/147708794.jpg'),
// //                     array('id' => '115685920', 'thumb'=> 'http://thumb1.shutterstock.com/photos/thumb_large/844213/115685920.jpg'),
// //                     array('id' => '110596781', 'thumb'=> 'http://thumb7.shutterstock.com/photos/thumb_large/844213/110596781.jpg'),
// //                     array('id' => '103893548', 'thumb'=> 'http://thumb1.shutterstock.com/photos/thumb_large/844213/103893548.jpg'),
// //                     array('id' => '103666919', 'thumb'=> 'http://thumb7.shutterstock.com/photos/thumb_large/844213/103666919.jpg'),
// //                     array('id' => '142830772' , 'thumb'=> 'http://thumb9.shutterstock.com/thumb_large/844213/142830772/stock-vector-vector-travel-car-142830772.jpg'), 
// //                     array('id' => '1331049108', 'thumb'=> 'http://thumb9.shutterstock.com/photos/thumb_large/844213/844213,1331049108,2.jpg'))
// //     ));







