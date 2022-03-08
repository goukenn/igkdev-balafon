<?php
namespace IGK\System\WinUI\Menus;

use IGK\System\Html\Dom\HtmlNode;

/**
 * represent the menu engine
 * @package IGK\System\WinUI\Menus
 */
class Engine{
    /**
     * @param IGK\System\WinUI\Menus\HtmlNode $hi 
     * @param string $text 
     * @param string $u 
     * @param bool $ajx 
     * @param mixed $options 
     * @return void 
     */
    public static function BuildMenuItem(HtmlNode $hi, string $text, string $u="#", bool $ajx=false, $options=null  ){
        if($ajx){
            $hi->addAJXA($u)->Content= $text;
        }
        else
            $hi->addA($u)->Content= $text;
    }

    public function buildSubMenuItem(HtmlNode $hi ){
        
    }   
    public function buildItem(HtmlNode $hi, string $text, string $u="#", bool $ajx=false, $options=null  ){
        return self::BuildMenuItem($hi, $text, $u, $ajx, $options);
    }
}