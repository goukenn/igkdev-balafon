<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Untitled-1
// @date: 20221122 12:39:48
// @desc: 
namespace IGK\System\Database;

use IGK\Database\DbColumnInfo;

/**
 * job is to cache definition to send to database 
 * @package IGK\System\Database
 */
class SQLObjectDef{
    private static $sm_defs;
    /**
     * 
     * @param mixed $values 
     * @return ?array 
     */
    public static function Resolve($values, bool $insert=true): ?array{
        $cl = get_class($values);
        $p = igk_getv(self::$sm_defs, $cl);
        if (is_null($p)){
            $cl = get_class($values);
            if (get_class($values) != \stdClass::class){
                $p = self::$sm_defs[$cl] = DbColumnInfo::CreateDefArrayFromClass($cl);
            } else {
                return (array)$values;
            }
        }
        $r = [];
        foreach($p as $k=>$v )
        {
            $rvalue = igk_getv($values, $v->clName);
            if ($insert){
                if ($v->clAutoIncrement)
                {
                    if (empty($rvalue)){
                        continue;
                    }
                } 
            }
            $r[$v->clName] = $rvalue; 

        }
        return $r;
    }
}
