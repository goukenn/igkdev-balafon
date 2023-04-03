<?php
// @author: C.A.D. BONDJE DOUE
// @filename: EnumeratesConstants.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Traits;

use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;

/**
 * enumerate constant values 
 */
trait EnumeratesConstants{

    /**
     * retrieve all constant value 
     * @return array of constant key=>value
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetConstants(){
        $ref = igk_sys_reflect_class(static::class);
        return $ref->GetConstants();
    }
    /**
     * get all constant keys
     * @return int[]|string[] 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetConstantKeys(){
        return array_keys(self::GetConstants());
    }
    /**
     * get constants values
     * @return array 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetConstantValues(){
        return array_values(self::GetConstants());
    }
    /**
     * get all constant value
     * @param mixed $k 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetConstantValue($k){
        $ref = igk_sys_reflect_class(static::class);
        return $ref->getConstant($k);
    }
    /**
     * get constant value type - name
     * @param mixed $ctrl 
     * @return mixed 
     */


    public static function GetName($ctrl){
        return $ctrl::name("");
    }
}