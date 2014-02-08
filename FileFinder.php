<?php
include_once "ShutterstockFiles.php";
include_once "FindeImages.php";
include_once "GetDataFromShutter.php";
include_once "PreCondition.php";
include_once "CashFiles.php";
include_once "ConvertToXml.php";
include_once "PostCondition.php";

class FileFinder
{
    public function FindData($FileThumbs, $Portfolio){
        $files = new ShutterstockFiles();
        $finder = new FindeImages();
        $getdata = new GetDataFromShutter();
        $cash = new CashFiles();
        $pre = new PreCondition();
        $toxml = new ConvertToXml();
        $post = new PostCondition();

        $time = microtime(true);
        $Pre = $pre->PreConditions($Portfolio);
        $Portfolio = $Pre['url'];
        $folder = $Pre['folder'];
        var_dump(microtime(true) - $time);
        $AllFilesShuter = $files->ShutterFiles($Portfolio, $folder);
        var_dump(microtime(true) - $time);
        $ID_deposit_shutter = $finder->FindeImage($FileThumbs, $AllFilesShuter, $folder);
        var_dump(microtime(true) - $time);
        $Data = $getdata->GetData($ID_deposit_shutter, $folder);
        var_dump(microtime(true) - $time);
        $Data = $toxml->convert($Data);
        var_dump(microtime(true) - $time);
        $post->PostConditions($folder);
        return $Data;
        //return "END! ".count($Data). " files found!!!";
    }
}


$compare = new FileFinder();
 var_dump($compare->FindData(
     array('11461317'=>'http://static9.depositphotos.com/1000270/1146/i/110/depositphotos_11461317-Freshly-baked-homemade-apple-pie.jpg',
        '9452106'=>'http://static8.depositphotos.com/1000270/945/i/110/depositphotos_9452106-Home-made-apple-and-strawberry-pie-ice-cream.jpg',
        '25860275'=>'http://st.depositphotos.com/1000270/2586/i/110/depositphotos_25860275-Homemade-apple-pie-marked-up-as-pie-chart.jpg',
        '1022762'=>'http://static3.depositphotos.com/1000270/102/i/170/depositphotos_1022762-Two-Hot-air-balloons-bumping.jpg',
        '8956663'=>'http://static8.depositphotos.com/1000270/895/i/170/depositphotos_8956663-Martin-Luther-King-Monument-DC.jpg',
        '2825740'=>'http://static4.depositphotos.com/1000270/282/i/170/depositphotos_2825740-Sphinx-and-Giza-Pyramids-in-Egypt.jpg',
        '17507811'=>'http://st.depositphotos.com/1000270/1750/i/170/depositphotos_17507811-Keep-off-dunes-sign-in-Florida.jpg',
        '13618273'=>'http://st.depositphotos.com/1000270/1361/i/170/depositphotos_13618273-Steeple-of-Fredericksburg-County-Courthouse.jpg'),
     'http://www.shutterstock.com/gallery-138433p2.html'
   ));    //1200 seconds!!!   

 // var_dump($compare->FindData(
 //     array('26995965'=>'http://st.depositphotos.com/1557418/2699/v/110/depositphotos_26995965-Vector-travel-car.jpg',
 //        '30194411'=>'http://st.depositphotos.com/1557418/3019/v/110/depositphotos_30194411-Funny-school-bus-illustration.jpg',
 //        '36794227'=>'http://st.depositphotos.com/1557418/3679/v/170/depositphotos_36794227-Car-icon-set.jpg',
 //        '18463163'=>'http://st.depositphotos.com/1557418/1846/v/170/depositphotos_18463163-Cinema-icon.jpg',
 //        '30194337'=>'http://st.depositphotos.com/1557418/3019/v/170/depositphotos_30194337-Vector-flat-icon.jpg',
 //        '25146565'=>'http://st.depositphotos.com/1557418/2514/v/170/depositphotos_25146565-Hot-chocolate-splash.jpg'),
 //     'http://www.shutterstock.com/cat.mhtml?gallery_id=844213'
 //    ));    // 250 seconds!!!

// var_dump($compare->FindData(
//     array('30748923'=>'http://st.depositphotos.com/1017397/3074/v/170/depositphotos_30748923-Map-of-Mauritania.jpg',
//         '33221117'=>'http://st.depositphotos.com/1017397/3322/v/170/depositphotos_33221117-Map-of-Zambia.jpg',
//         '22298643'=>'http://st.depositphotos.com/1017397/2229/v/170/depositphotos_22298643-Map-of-Peru.jpg',
//         '10183627'=>'http://static8.depositphotos.com/1017397/1018/i/170/depositphotos_10183627-Stone-man-in-Hermitage.jpg'),
//     'http://www.shutterstock.com/portfolio/search.mhtml?page=1&gallery_username=sateda&safesearch=0'
//     ));  // 380 seconds!!!

// var_dump($compare->FindData(
//     array('9455001'=>'http://static8.depositphotos.com/1484771/945/v/170/depositphotos_9455001-Vector-set-calligraphic-design-elements-and-page-decoration-1.jpg',
//         '9454968'=>'http://static8.depositphotos.com/1484771/945/v/170/depositphotos_9454968-Digital-scrapbooking-kit-old-paper.jpg',
//         '9476027'=>'http://static8.depositphotos.com/1484771/947/v/170/depositphotos_9476027-Vector-icons-animals-and-nature.jpg',),
//     'http://www.shutterstock.com/g/akaiser'
//     ));  // 109 seconds!!!

// var_dump($compare->FindData(
//      array('6149688-'=>'http://static6.depositphotos.com/1006472/614/i/110/depositphotos_6149688-Drunk-man.jpg',
//         '13613923'=>'http://st.depositphotos.com/1006472/1361/i/170/depositphotos_13613923-Protective-equipment.jpg',
//         '8419689'=>'http://static8.depositphotos.com/1006472/841/i/170/depositphotos_8419689-Owl-in-the-zoo.jpg',
//         '4132659'=>'http://static5.depositphotos.com/1006472/413/i/170/depositphotos_4132659-Pencils.jpg'),
//      'http://www.shutterstock.com/cat.mhtml?gallery_id=191161'
//     ));    // 2700 seconds



