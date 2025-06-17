<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModelConstantsHelper.php
// @date: 20230120 12:02:06
namespace IGK\Models\Traits;

use IGK\Helper\Database;
use ReflectionClass;

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
        $model = igk_getv($cl_vars = get_class_vars($cl), 'model');
        $fn = igk_getv($cl_vars, 'field_name');
        return $model::GetCache($fn, $value);
    }

    /**
     * init data
     * @return void 
     */
    public static function InitData(){
        /**
         * @var mixed|string $cl
         */
        $fc = Database::InsertExtraFieldsMethod;
        $cl = static::class;
        $tmodel = igk_getv($cl_vars = get_class_vars($cl), 'model') ?? igk_die(sprintf('missing required model.[%s]', static::class));
        $tfn = igk_getv($cl_vars, 'field_name');
        $model = $tmodel;// cl::$model;
        $fn = $tfn; // cl::$field_name;
        $init_fields = method_exists(static::class, $fc);
        $v_constants = $cl::GetConstants();
        foreach($v_constants as $ut){
            $fields = [
                $fn=>$ut
            ];
            if ($init_fields ){
                $r = (object)['fields'=> & $fields];
                call_user_func_array([static::class, $fc], [$r, $ut]);
            }
            $model::createIfNotExists($fields);
        }
    }
}