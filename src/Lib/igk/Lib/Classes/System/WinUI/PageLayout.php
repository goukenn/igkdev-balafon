<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PageLayout.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\WinUI;

use IGK\System\Configuration\ConfigData;

/**
 * represent a page default layout
 * @package IGK\System\WinUI
 */
class PageLayout{
    const Limits = [20,50,100];
    const CurrentLimit = 20;
    /**
     * store layout compiler options
     * @var object
     */
    var $options = [];
    /**
     * store views layout
     * @var mixed
     */
    var $viewDir;
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
    public function __set($name, $value)
    {
        $this->options[$name] = $value;
    }
    public function __get($name){
        return igk_getv($this->options, $name);
    }
}