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
        $a = $ajx ? $hi->addAJXA($u) : $hi->addA($u);
        $a->Content = $text;
        igk_hook("filter-menu-item", ["item"=>$a, "ajx"=>$ajx]);
    }

    public function buildSubMenuItem(HtmlNode $hi ){
        
    }   
    public function buildItem(HtmlNode $hi, string $text, string $u="#", bool $ajx=false, $options=null  ){
        return self::BuildMenuItem($hi, $text, $u, $ajx, $options);
    }
}