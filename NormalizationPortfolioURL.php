<?php 
class NormalizationPortfolioURL
{
	public function GetFileUrlFromPortfolio($PortfolioURL){
        $page = file_get_contents($PortfolioURL);
        preg_match_all('/href="\/pic.*/', $page, $file);
        $file = preg_replace(array('/href="/', '/html.*/'), "", $file[0][0])."html";
        return "http://www.shutterstock.com".$file;
    }

    public function GetPortfolioUrlFromFilePage($filePage){
        $page = file_get_contents($filePage);
        preg_match_all('/id="portfolio_link.*/', $page, $file);
        $PortfolioURL = preg_replace(array('/.*href="/','/">/'), "", $file[0][0]);

        return "http://www.shutterstock.com".$PortfolioURL;
    }
/*
    $PortfolioURL - URL of portfolio on shutter
    return: NORMAL url of portfofio on shutter
*/
    public function GetNormalURL($PortfolioURL){
        $filePage = $this->GetFileUrlFromPortfolio($PortfolioURL);
        $PortfolioURL = $this->GetPortfolioUrlFromFilePage($filePage);
        return $PortfolioURL;
    }
}