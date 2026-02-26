<?php
// @author: C.A.D. BONDJE DOUE
// @file: EventHostTrait.php
// @date: 20260206 13:29:18
namespace IGK\System\Core\Traits;

use IGK\System\Core\AppEvent;

/**
* 
* @package IGK\System\Core\Traits
* @author C.A.D. BONDJE DOUE
*/
trait EventHostTrait{

    /**
    * Returns Event Object.
    * @return array
    */
    protected abstract function getEventObject():array;
    /**
     * remove event 
     * @param string $name 
     * @param callable $callback 
     * @return void 
     */

    public function removeEvent(string $name, ?callable $callback, bool $all = true)
    {
        if (($g = igk_getv($this->getEventObject(), $name)) instanceof AppEvent) {
            $g->remove($callback, $all);
        }
    }

    /**
     * add event 
     * @param string $name 
     * @param callable $callback 
     * @return void 
     */

    public function addEvent(string $name, callable $callback)
    {
        if (($g = igk_getv($this->getEventObject(), $name)) instanceof AppEvent) {
            $g->add($callback);
        } else {
            igk_die(sprintf(__('missing event object [%s]'), $name));
        }
    }
}