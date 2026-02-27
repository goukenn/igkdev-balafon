<?php
// @author: C.A.D. BONDJE DOUE
// @file: Delegate.php
// @date: 20250808 14:30:02
namespace IGK\System;

use Closure;

/**
* delegate to chain some actions 
* @package IGK\System
* @author C.A.D. BONDJE DOUE
*/
abstract class Delegate{

    /**
    * Collection of list.
    * @var mixed
    */
    private $m_list = [];

    /**
    * .ctr
    */
    protected function __construct(){        
    }
    /**
     * override declaration to force parameter definitions
     * @return void 
     */

    public function __invoke()
    {
        $args = func_get_args();
        $tab = $this->m_list;
        while(0<count($tab)){
            $q = array_shift($tab);
            call_user_func_array($q, $args);
        }
    }
    /**
     * create the delegate
     * @param null|Closure $initial_closure 
     * @return static 
     */

    public static function CreateDelegate(?Closure $initial_closure=null){
        $e = new static;
        if (!is_null($initial_closure)){
            $e->m_list[] = $initial_closure;
        }
        return $e;
    }

    /**
    * Adds.
    * @param Closure $closure
    */
    public function add(Closure $closure){
         $this->m_list[] = $closure;
    }

    /**
    * Clears.
    */
    public function clear(){

        $this->m_list = [];
    }
    /**
     * count number off all closured 
     * @return int 
     */

    public function getCount():int{
        return count($this->m_list);
    }

    /**
    * auto generate doc.
    * @param bool $all
    * @return void
    */

    public function remove(Closure $closure, $all= false){
        $tab = & $this->m_list;
        while(false !== ($idx = array_search($closure, $this->m_list))){
            unset($tab[$idx]);
            if (!$all) break;
        }
    }
}