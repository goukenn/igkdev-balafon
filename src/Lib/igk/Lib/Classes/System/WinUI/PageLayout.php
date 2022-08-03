<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PageLayout.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\WinUI;

use IGK\System\Configuration\ConfigData;

class PageLayout{
    const Limits = [20,50,100];
    const CurrentLimit = 20;
    /**
     * get 
     * @return ConfigData|int 
     */
    public static function ItemLimits(){
        $limit = igk_configs()->get("pagelayout_limit");
        if ($limit>0){
            return $limit;
        }
        return self::CurrentLimit;
    }
}