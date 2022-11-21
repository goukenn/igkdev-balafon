<?php
// @author: C.A.D. BONDJE DOUE
// @file: InvocatorListDelegate.php
// @date: 20221117 11:41:35
namespace IGK\System\Delegates;


///<summary></summary>
/**
* 
* @package IGK\System\Delegates
*/
class InvocatorListDelegate{
    private $m_callback;
    private $m_hostlist;
    private static $sm_shared_intances;
    public function getItems(){
        return $this->m_hostlist;
    }
    private function __construct()
    {
        
    }
    public static function Create(array $array, callable $callback){
        $invocator = new self;        
        $invocator->m_callback = $callback;
        $invocator->m_hostlist = $array;
        self::$sm_shared_intances = $invocator;
        return $invocator;
    }
   
    public function __call($name, $arguments){
        foreach($this->m_hostlist as $b){
            call_user_func_array([$b, $name], $arguments);
        }
    }   
    public static function __callStatic($name, $argument){
        if ($invocator = self::$sm_shared_intances){
            $list =  $invocator->m_hostlist;
            $fc = $invocator->m_callback;
            foreach($list as $m){
                $fc($m, $name, $argument);
            }
        }
    }
}