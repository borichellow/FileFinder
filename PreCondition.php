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

    public function PreConditions($file){
        $file = json_decode(file_get_contents($file),true);
        $file['shutterstock_profile_url'] = $this->GetNormalURL($file['shutterstock_profile_url']);
        $folder = $file['user_id'];
        exec("mkdir ".$folder);
        exec("mkdir ".$folder."/ShutterFiles");
        exec("mkdir ".$folder."/Results");
        exec("mkdir ".$folder."/Data");
        return $file;
    }
}

// $norm = new NormalizationPortfolioURL();
// $norm->PreConditions("http://www.shutterstock.com/cat.mhtml?gallery_id=844213");