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

    /**
    * auto generate doc.
    * @var mixed
    */
    const Limits = [20,50,100];

    /**
    * auto generate doc.
    * @var mixed
    */
    const CurrentLimit = 20;
    /**
     * store layout compiler options
     * @var object
     */
    var $options = [];
    /**
     * store custom view layout
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

    /**
    * destructor
    * @param mixed $name
    * @param mixed $value
    */
    public function __set($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
    * .destructor
    * @param mixed $name
    */
    public function __get($name){
        return igk_getv($this->options, $name);
    }
}