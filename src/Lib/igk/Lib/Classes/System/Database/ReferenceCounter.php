<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReferenceCounter.php
// @date: 20230202 16:53:35
namespace IGK\System\Database;

use IGK\Models\ModelBase;
use IGKEvents;

///<summary></summary>
/**
 * use to update counter 
 * @package IGK\System\Database
 */
class ReferenceCounter
{
    private static $sm_refCallback = [];
    private static $REF_UPDATE;
    /**
     * 
     * @param ModelBase $data 
     * @param callable $callback 
     * @return void 
     */
    public static function Register(ModelBase $data, callable $callback)
    {
        self::$sm_refCallback[] = [
            'c' => $callback,
            'd' => $data
        ];
        if (is_null(self::$REF_UPDATE)) {

            
            self::$REF_UPDATE = function ($e) use ($data) {
                $row = $e->args['row'];
                $s = get_class($row);
                $ss= get_class($data);      
                
                foreach (self::$sm_refCallback as $v) {
                    $c = $v['c'];
                    $d = $v['d'];
                    if ($c($row)) {
                        $d->save();
                        self::Unregister();
                    }
                }
            };
            igk_reg_hook(IGKEvents::HOOK_DB_INSERT, self::$REF_UPDATE);
        }
    }

    public static function Unregister(){
        if (self::$REF_UPDATE){
            igk_unreg_hook(IGKEvents::HOOK_DB_INSERT, self::$REF_UPDATE);
        }
        self::$REF_UPDATE = null;
        self::$sm_refCallback = [];
    }
}
