<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssEnvironment.php
// @date: 20230314 11:53:48
namespace IGK\System\Html\Css;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Css
*/
abstract class CssEnvironment{
    const KEY = __CLASS__.":/init";
    public static function GetInitClass(string $cl){
        static $sm_init;
        if (is_null($sm_init)){
            self::_InitClassStyle();
        }
        $g = igk_environment()->get(self::KEY) ?? [];
        if ($c =  igk_getv($g, $cl)){
            return $c;
        }
        $g[$cl] = 'igk-'.igk_css_str2class_name($cl);
        igk_environment()->set(self::KEY, $g);
        return $g[$cl];
    }
    private static function _InitClassStyle(){
        $tab = [];
        $tab['button'] = 'igk-btn';
        igk_environment()->set(self::KEY, $tab);
    }
}