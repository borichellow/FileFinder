<?php 
class GetDataFromShutter
{
    private function waiter($results, $folder){
        echo "Waiting fot workers...Geter Data from Shutter\n";
        do{
            $flag = "yes";
            foreach ($results as $file) {
                if(!(file_exists("./".$folder."/Data/".$file.".txt"))){
                    $flag = "no";
                    break;
                }
            }
        }while($flag == "no");
    }

    private function resultAnalis($results, $folder, &$fileData){
        echo "Parsing Data from Shutter...\n";
        foreach ($results as $deposit => $shutter) {
            $data = json_decode(file_get_contents("./".$folder."/Data/".$shutter.".txt"), true);
            $fileData[] = array('item_id' => $deposit, 'title' => $data['title'], 'keywords' => $data['keywords'],
                                 'shutterstock_item_id' => $shutter);
        }
    }
/*
    $ID_deposit_shutter:  array('deposit' => depositID, 'shutter' => shutterID)
    $fileData:  array('item_id' , 'title', 'keywords', 'shutterstock_item_id')
*/

    public function GetData($ID_deposit_shutter, $folder){
        $i = 0;
        $count = count($ID_deposit_shutter);
        $fileData = array();
        while ($i < $count) {
            $results = array();
            for ($ii = 0 ; $ii < 200 ; $ii++){
                $results[$ID_deposit_shutter[$i]['deposit']] = $ID_deposit_shutter[$i]['shutter'];
                exec("php workerGeter.php ".$ID_deposit_shutter[$i]['shutter']." ".$folder." >> /dev/null &");
                $i ++;
                if($i == $count){break;}
            }
            $this->waiter($results, $folder);
            $this->resultAnalis($results, $folder, $fileData);
        }
        return $fileData;
    }
}