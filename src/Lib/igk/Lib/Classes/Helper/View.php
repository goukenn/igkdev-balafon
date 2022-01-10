<?php

namespace IGK\Helper;

use IGK\Controllers\BaseController;
use IGKEnvironment;
use IGKException;

/**
 * view helper class 
 * @package
 * @method string File() get current view file 
 * @method IGKHtmlDoc Doc() get current document
 * @method HtmlNode TargetNode() get current target node
 */
class View {
    /**
     * get include file
     * @return string
     */
    public static function File(){
        return igk_get_viewfile();
    }
    /**
     * get included file directory
     * @return string 
     * @throws IGKException 
     */
    public static function Dir(){
        return dirname(self::File());
    }
    /**
     * get current controller
     * @return null|BaseController current controller
     */
    public static function CurrentCtrl(): ?BaseController{
        return igk_environment()->get(IGKEnvironment::CURRENT_CTRL);
    }

    public static function GetArgs($n = null, $default=null){
        $s = igk_environment()->get("sys://io/query_args");
        if ($n == null)
            return $s;
        return igk_getv($s, $n, $default);
    }
}