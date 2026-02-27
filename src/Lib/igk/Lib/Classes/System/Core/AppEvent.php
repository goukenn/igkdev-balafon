<?php
// @author: C.A.D. BONDJE DOUE
// @file: AppEvent.php
// @date: 20260206 13:08:29
namespace IGK\System\Core;

use IGKEvents;

/**
* auto generate doc.
* @package IGK\System\Core
* @author C.A.D. BONDJE DOUE
*/
class AppEvent
{
    private function _getkey()
    {
        return 'obj-event://' . spl_object_id($this);
    }

    /**
    * Invoke.
    * @param mixed $sender
    * @param mixed $args
    */
    public function invoke($sender, $args)
    {
        $k = $this->_getkey();
        igk_hook($k, ['event'=>$args]);
    }
    /**
     * add event callback
     * @param callable $callback 
     * @return void 
     */

    public function add(callable $callback)
    {
        $k = $this->_getkey();
        igk_reg_hook($k, $callback);
    }
    /**
     * remove event callback 
     * @param ?callable $callback 
     * @param bool $all 
     * @return void 
     */

    public function remove(?callable $callback, bool $all = true)
    {
        $k = $this->_getkey();
        igk_unreg_hook($k, $callback, $all);
    }

    /**
    * Clears.
    */
    public function clear(){
        return $this->remove(null, true);
    }
}
