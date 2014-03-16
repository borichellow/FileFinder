<?php
include_once "ShutterstockFiles.php";
include_once "FindeImagesFork.php";
include_once "FindeImages.php";
include_once "FindeImages2.php";
include_once "GetDataFromShutter.php";
include_once "PreCondition.php";
include_once "CashFiles.php";
include_once "CashFilesFork.php";
include_once "ConvertRezult.php";
include_once "PostCondition.php";

class FileFinder
{
    public function FindData($File){
        $files = new ShutterstockFiles();
        $finder = new FindeImages2();
        $getdata = new GetDataFromShutter();
        $cash = new CashFiles();
        $pre = new PreCondition();
        $convert = new ConvertRezult();
        $post = new PostCondition();

        $time = microtime(true);
        $Pre = $pre->PreConditions($File);
        $Portfolio = $Pre['shutterstock_profile_url'];
        $folder = $Pre['user_id'];
        $FileThumbs = $Pre['items'];
        print "Time for PreConditions: ".(microtime(true) - $time). " seconds\n";
        $AllFilesShuter = $files->ShutterFiles($Portfolio, $folder);
        print "Time for Getting portfolio from shutter: ".(microtime(true) - $time). " seconds\n";
        $AllFilesShuter = $cash->CashShutter($AllFilesShuter, $folder);
        $FileThumbs = $cash->CashDeposit($FileThumbs, $folder);
        print "Time for Cashing: ".(microtime(true) - $time). " seconds\n";
        $ID_deposit_shutter = $finder->FindeImage($FileThumbs, $AllFilesShuter, $folder);
        print "Time for Search of files: ".(microtime(true) - $time). " seconds\n";
        $Data = $getdata->GetData($ID_deposit_shutter, $folder);
        print "Time for Getting Data from shutter: ".(microtime(true) - $time). " seconds\n";
        $Data = $convert->convertToJson($Data, $folder, $AllFilesShuter);
        $post->PostConditions($folder);
        print "!!!END!!! \nFull time of working: ".(microtime(true) - $time). " seconds\n"
                .count($ID_deposit_shutter)." files from ".count($Pre['items']). " were found\n";
        return $Data;
        //return "END! ".count($Data). " files found!!!";
    }
}


$compare = new FileFinder();
$compare->FindData($argv[1]);
//var_dump($compare->FindData("./1.txt"));   // 250 sec
//var_dump($compare->FindData("./2.txt"));   //1200 sec
//var_dump($compare->FindData("./3.txt"));   // 380 sec
