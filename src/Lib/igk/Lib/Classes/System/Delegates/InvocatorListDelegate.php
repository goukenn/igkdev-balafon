<?php
// @author: C.A.D. BONDJE DOUE
// @file: InvocatorListDelegate.php
// @date: 20221117 11:41:35
namespace IGK\System\Delegates;
/**
* 
* @package IGK\System\Delegates
*/
class InvocatorListDelegate{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_callback;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_hostlist;

    /**
    * auto generate doc.
    * @var mixed
    */
    private static $sm_shared_intances;

    /**
    * auto generate doc.
    */
    public function getItems(){
        return $this->m_hostlist;
    }
    private function __construct()
    {
    }

    /**
    * auto generate doc.
    * @param array $array
    * @param callable $callback
    */
    public static function Create(array $array, callable $callback){
        $invocator = new self;        
        $invocator->m_callback = $callback;
        $invocator->m_hostlist = $array;
        self::$sm_shared_intances = $invocator;
        return $invocator;
    }

    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $arguments
    */
    public function __call($name, $arguments){
        foreach($this->m_hostlist as $b){
            call_user_func_array([$b, $name], $arguments);
        }
    }

    /**
    * Triggered when calling an inaccessible or undefined static method.
    * @param mixed $name
    * @param mixed $argument
    */
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