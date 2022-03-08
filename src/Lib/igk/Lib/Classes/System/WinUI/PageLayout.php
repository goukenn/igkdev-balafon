<?php
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
        $limit = igk_sys_configs()->get("pagelayout_limit");
        if ($limit>0){
            return $limit;
        }
        return self::CurrentLimit;
    }
}