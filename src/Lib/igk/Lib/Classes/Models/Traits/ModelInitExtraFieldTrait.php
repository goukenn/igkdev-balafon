<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModelInitExtraFieldTrait.php
// @date: 20260620 17:22:54
namespace IGK\Models\Traits;


/**
* 
* @package IGK\Models\Traits
* @author C.A.D. BONDJE DOUE
*/
trait ModelInitExtraFieldTrait{
 /**
     * 
     * @param string $name 
     * @return mixed 
     */
    public abstract static function GetInitExtraField(string $name);
    /**
     * to insert extra fields
     * @param mixed $r 
     * @param mixed $name 
     * @return void 
     */
    public static function InsertExtraFields($r, $name){
        $tab = self::GetInitExtraField($name);
        $r->fields = array_merge($r->fields, $tab);
    }
}