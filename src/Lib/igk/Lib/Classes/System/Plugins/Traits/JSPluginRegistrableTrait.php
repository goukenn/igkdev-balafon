<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSPluginRegistrableTrait.php
// @date: 20230316 21:22:01
namespace IGK\System\Plugins\Traits;


///<summary></summary>
/**
* 
* @package IGK\System\Plugins\Traits
*/
trait JSPluginRegistrableTrait{
    private $m_registry;

    protected function jsPluginRegister(){
    }
    protected function jsPluginUnregister(){
        if ($this->m_registry){
            $this->m_registry->unregister($this);
        }
    }
}