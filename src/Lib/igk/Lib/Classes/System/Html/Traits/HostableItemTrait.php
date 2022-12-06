<?php
// @author: C.A.D. BONDJE DOUE
// @file: HostableItemTrait.php
// @date: 20221123 18:19:15
namespace IGK\System\Html\Traits;

use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\HtmlUtils;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Traits
*/
trait HostableItemTrait{
    /**
     * hotable node item 
     * @param callable $n 
     * @param mixed $args 
     * @return void 
     */
    public function host(callable $callback, ...$args){
        HtmlUtils::HostNode($this, $callback, ...$args);
        return $this;
    }
}