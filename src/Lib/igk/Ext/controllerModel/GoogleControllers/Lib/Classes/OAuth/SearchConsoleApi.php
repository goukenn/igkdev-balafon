<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SearchConsoleApi.php
// @date: 20220803 13:48:59
// @desc: 


namespace IGK\Core\Ext\Google\OAuth;

class SearchConsoleApi{
    const ENTRY_URI = "https://www.googleapis.com/webmasters/v3";

    public function addSite($site, $gclient, $tokeninfo){
        $r = igk_curl_post_uri(self::ENTRY_URI."/sites/".urlencode($site)."?key=".$gclient->api_key,null, ["PUT"=>1],
        ["Authorization: {$tokeninfo->token_type} {$tokeninfo->access_token}"]);
        return json_decode($r);
    }
}