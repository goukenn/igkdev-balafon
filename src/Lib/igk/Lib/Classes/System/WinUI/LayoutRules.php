<?php
// @author: C.A.D. BONDJE DOUE
// @file: LayoutRules.php
// @date: 20240911 10:54:00
namespace IGK\System\WinUI;

use Exception;
use IGK\Models\Configurations;
use IGKException;
use ReflectionClass;

///<summary></summary>
/**
* 
* @package IGK\System\WinUI
* @author C.A.D. BONDJE DOUE
* @initdb()
*/
class LayoutRules{
    const TEXTAREA_HEIGHT = '18rem';
    const PADDING = '10px';
    /**
     * get system layout rules
     * @param mixed $name 
     * @return mixed 
     * @throws Exception 
     * @throws IGKException 
     */
    public static function Get($name){
        $p = igk_sys_reflect_class(static::class);
        $const = $p->getConstants();
        $v = igk_getv($const, $name); 
        if ($r = Configurations::GetCache('clName', strtolower(sprintf('winui.%s',$name)))){
            $v = $r->clValue ?? $v;
        } 
        return $v;
    } 
}