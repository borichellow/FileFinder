<?php 
class PreCondition
{
	private function GetFileUrlFromPortfolio($PortfolioURL){
        $page = file_get_contents($PortfolioURL);
        preg_match_all('/href="\/pic.*/', $page, $file);
        $file = preg_replace(array('/href="/', '/html.*/'), "", $file[0][0])."html";
        return "http://www.shutterstock.com".$file;
    }

    private function GetPortfolioUrlFromFilePage($filePage){
        $page = file_get_contents($filePage);
        preg_match_all('/id="portfolio_link.*/', $page, $file);
        $PortfolioURL = preg_replace(array('/.*href="/','/">/'), "", $file[0][0]);

        return "http://www.shutterstock.com".$PortfolioURL;
    }
/*
    $PortfolioURL - URL of portfolio on shutter
    return: NORMAL url of portfofio on shutter
*/
    private function GetNormalURL($PortfolioURL){
        $filePage = $this->GetFileUrlFromPortfolio($PortfolioURL);
        $PortfolioURL = $this->GetPortfolioUrlFromFilePage($filePage);
        return $PortfolioURL;
    }

    public function PreConditions($PortfolioURL){
        $PortfolioURL = $this->GetNormalURL($PortfolioURL);
        $folder = preg_replace(array("/http:\/\/www.shutterstock.com\/gallery-/", "/\.html/", "/p1/"), "", $PortfolioURL);
        //var_dump($folder);
        exec("mkdir ".$folder);
        exec("mkdir ".$folder."/ShutterFiles");
        exec("mkdir ".$folder."/Results");
        exec("mkdir ".$folder."/Data");
        return array('url' => $PortfolioURL, 'folder' => $folder);
    }
}

// $norm = new NormalizationPortfolioURL();
// $norm->PreConditions("http://www.shutterstock.com/cat.mhtml?gallery_id=844213");