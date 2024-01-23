<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Engine.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\WinUI\Menus;

use IGK\Controllers\BaseController;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\WinUI\Traits\ResolveUriTrait;
use IGKException;

/**
 * represent the menu engine
 * @package IGK\System\WinUI\Menus
 */
class Engine{
    use ResolveUriTrait;
    private static $sm_register;
    public static function RegisterIconEngineCallback(string $name, $callback){
        if (is_null(self::$sm_register)){
            self::$sm_register = [];
        }
        return self::$sm_register[$name] = $callback;
    }
    public static function GetIconEngineCallback(string $name){
        return self::$sm_register ? igk_getv(self::$sm_register, $name) : null;
    }
    /**
     * @param IGK\System\WinUI\Menus\HtmlNode $hi 
     * @param string $text 
     * @param string $u 
     * @param bool $ajx 
     * @param mixed $options 
     * @return void 
     */
    public static function BuildMenuItem(HtmlNode $hi, string $text, string $u="#", bool $ajx=false, $options=null  ){
        $a = $ajx ? $hi->addAJXA($u) : $hi->addA($u);
        $icon = $options ? igk_getv($options, 'icon') : null;
        $v_class_name = $options ? igk_getv($options, 'class') : null;
        if ($icon){
            if (is_string($icon)){
                $ref = explode("::", $icon, 2);
                if ($fc = self::GetIconEngineCallback($ref[0])){
                    $icon = $fc($ref[1]);
                }
                
            }
            $a->add($icon);
        }
        $a->text($text); 
        $a->className = $v_class_name;
        
        igk_hook("filter-menu-item", ["item"=>$a, "ajx"=>$ajx]);
    }

    public function buildSubMenuItem(HtmlNode $hi ){  
        throw new IGKException('not implement '.__METHOD__);     
    }   
    public function buildItem(HtmlNode $hi, string $text, string $u="#", bool $ajx=false, $options=null  ){
        return self::BuildMenuItem($hi, $text, $u, $ajx, $options);
    }
    
}