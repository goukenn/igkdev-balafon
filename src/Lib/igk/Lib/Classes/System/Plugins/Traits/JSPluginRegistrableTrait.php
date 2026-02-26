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
    * auto generate doc.
    * @var mixed
    */
    private $m_registry;

    /**
    * auto generate doc.
    */
    protected function jsPluginRegister(){
    }

    /**
    * auto generate doc.
    */
    protected function jsPluginUnregister(){
        if ($this->m_registry){
            $this->m_registry->unregister($this);
        }
    }
}