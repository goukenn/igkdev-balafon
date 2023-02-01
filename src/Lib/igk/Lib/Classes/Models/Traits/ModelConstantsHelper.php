<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModelConstantsHelper.php
// @date: 20230120 12:02:06
namespace IGK\Models\Traits;


///<summary></summary>
/**
* class must provide a static $model and static $field_name
* @package IGK\Models\Traits
*/
trait ModelConstantsHelper{
    /**
     * model class to use
     * @var mixed
     */
    // protected static $model;

    // /**
    //  * field name to use
    //  * @var mixed
    //  */
    // protected static $field_name;
    /**
     * 
     * @param mixed $value 
     * @return mixed 
     */
    public static function GetCacheData($value){
        /**
         * @disable 1014
         */
        return static::$model::GetCache(static::$field_name, $value);
    }
}