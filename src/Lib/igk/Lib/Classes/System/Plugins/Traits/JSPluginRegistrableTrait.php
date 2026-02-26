<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSPluginRegistrableTrait.php
// @date: 20230316 21:22:01
namespace IGK\System\Plugins\Traits;
/**
* 
* @package IGK\System\Plugins\Traits
*/
trait JSPluginRegistrableTrait{

    /**
    * Property: registry.
    * @var mixed
    */
    private $m_registry;

    /**
    * Js plugin register.
    */
    protected function jsPluginRegister(){
    }

    /**
    * Js plugin unregister.
    */
    protected function jsPluginUnregister(){
        if ($this->m_registry){
            $this->m_registry->unregister($this);
        }
    }
}