<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbColumnInfoMethodTrait.php
// @date: 20221104 14:47:37
namespace IGK\Database\Traits;


///<summary></summary>
/**
* 
* @package IGK\Database\Traits
*/
trait DbColumnInfoMethodTrait{
    ///<summary>get if this is unsigned type</summary>
    /**
     * get if this is unsigned type
     * @return int|false 
     */
    public function IsUnsigned():bool{
        return preg_match("/u(((big|smal|tiny)?int)|float)/i", $this->clType);
    }
    public function getIsRefId():bool{
        return preg_match("/int/i", $this->clType ) && $this->clAutoIncrement && $this->clIsPrimary;
    }  
    /**
     * check if support type length
     * @param mixed $t 
     * @return int|false 
     */
    public static function SupportTypeLength($t):bool{
        // return preg_match("/(int|varchar|char|enum|guid)/i", strtolower($t));
        return preg_match("/((u(big|smal|tiny))?int|varchar|char|enum|guid)/i", strtolower($t));
    }
}