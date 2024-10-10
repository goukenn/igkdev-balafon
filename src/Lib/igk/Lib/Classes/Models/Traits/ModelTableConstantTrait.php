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
trait ModelTableConstantTrait{
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
        $fc = 'InsertExtraFields';
        $cl = static::class;
        $model = $cl::$model;
        $init_fields = method_exists(static::class, $fc);
        foreach($cl::GetConstants() as $ut){
            $fields = [
                $cl::$field_name=>$ut
            ];
            if ($init_fields ){
                $r = (object)['fields'=> & $fields];
                call_user_func_array([static::class, $fc], [$r, $ut]);
            }
            $model::createIfNotExists($fields);
        }
    }
}