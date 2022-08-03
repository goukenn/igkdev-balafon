<?php
// @author: C.A.D. BONDJE DOUE
// @filename: EnumeratesConstants.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Traits;

use ReflectionClass;

trait EnumeratesConstants{
    public static function GetConstants(){
        $ref = new ReflectionClass(static::class);
        return $ref->GetConstants();
    }
    public static function GetConstantKeys(){
        return array_keys(self::GetConstants());
    }
    public static function GetConstantValue($k){
        $ref = new ReflectionClass(static::class);
        return $ref->getConstant($k);
    }
}