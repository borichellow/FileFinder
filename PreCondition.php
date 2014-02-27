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

    private function GetNormalURL($PortfolioURL){
        $filePage = $this->GetFileUrlFromPortfolio($PortfolioURL);
        $PortfolioURL = $this->GetPortfolioUrlFromFilePage($filePage);
        return $PortfolioURL;
    }

    private function NormalizationItemsArray($ItemsArray){
        $newItems = array();
        foreach ($ItemsArray as $item) {
            $handle = curl_init($item['thumb']);
            curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
            /* Get the HTML or whatever is linked in $url. */
            $response = curl_exec($handle);
            /* Check for 404 (file not found). */
            $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            if($httpCode != 404) {
                $newItems[$item['item_id']] = $item['thumb'];
            }
            curl_close($handle);
        }
        return $newItems;
    }
/*
    $file - name of file
*/
    public function PreConditions($file){
        $file = json_decode(file_get_contents($file),true);
        $file['shutterstock_profile_url'] = $this->GetNormalURL($file['shutterstock_profile_url']);
        $file['items'] = $this->NormalizationItemsArray($file['items']);
        $folder = $file['user_id'];
        exec("mkdir ".$folder);
        exec("mkdir ".$folder."/ShutterFiles");
        exec("mkdir ".$folder."/Results");
        exec("mkdir ".$folder."/DepositFiles");
        exec("mkdir ".$folder."/Data");
        return $file;
    }
}

// $norm = new NormalizationPortfolioURL();
// $norm->PreConditions("http://www.shutterstock.com/cat.mhtml?gallery_id=844213");