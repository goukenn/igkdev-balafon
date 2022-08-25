<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ViewCallback.php
// @date: 20220814 09:19:43
// @desc: 

namespace IGK\Helper;

use IGK\Controllers\ViewLayoutCaller;

/**
 * view callback helper
 * @package IGK\Helper
 */
abstract class ViewCallback{
   
    public static function __callStatic($name, $arguments)
    {
        if (!($ctrl = ViewHelper::CurrentCtrl())){
            igk_die("can't call it on on view context");
        }
        $layout = $ctrl->getViewLoader();
        // igk_wln_e("current.....", $layout);
        if (method_exists($layout, $name)){
            $lc = new ViewLayoutCaller;
            $lc->name = $name;
            $lc->arguments = $arguments;
            $lc->host = $layout;
            return [$lc, "invoke"];
        }
        return function($n)use($name){
            if (igk_environment()->isDev()){
                $n->panelbox()->setClass("igk-danger")->Content = "Layout do not support : ".$name;
            }
        };
    }    
}
