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
    // /**
    //  * model class to use
    //  * @var mixed
    //  */
    // protected static $model;

    // /**
    //  * field name to use
    //  * @var mixed
    //  */
    //  protected static $field_name;
    /**
     * 
     * @param mixed $value 
     * @return mixed 
     */
    public static function GetCacheData($value){
        /**
         * @disable 1014
         */
        /** eslint-disable */
        $cl = static::class;
        return $cl::$model::GetCache($cl::$field_name, $value);
    }

    /**
     * init data
     * @return void 
     */
    public static function InitData(){
        $cl = static::class;
        $model = $cl::$model;
        foreach($cl::GetConstants() as $ut){
            $model::createIfNotExists([
                $cl::$field_name=>$ut
            ]);
        }
    }
}