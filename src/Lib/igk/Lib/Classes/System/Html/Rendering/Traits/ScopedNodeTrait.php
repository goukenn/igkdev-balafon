<?php
// @author: C.A.D. BONDJE DOUE
// @file: ScopedNodeTrait.php
// @date: 20241016 13:35:45
namespace IGK\System\Html\Rendering\Traits;
/**
* 
* @package IGK\System\Html\Rendering\Trait
* @author C.A.D. BONDJE DOUE
*/
trait ScopedNodeTrait{

    /**
    * Property: before render.
    * @var mixed
    */
    private $m_beforeRender;

    /**
    * Property: after render.
    * @var mixed
    */
    private $m_afterRender;
       /**
     * set before callback listener 
     * @param ?callable $callback 
     * @return void 
     */

    public function beforeRender($callback){
        $this->m_beforeRender = $callback;
    }

    /**
    * After render.
    * @param mixed $callable
    */
    public function afterRender($callable){
        $this->m_afterRender = $callable;
    }

    /**
    * Before render callback.
    * @param mixed $options
    * @param mixed $setting
    */
    public function beforeRenderCallback($options, $setting) {
        if ($c = $this->m_beforeRender){            
            $c($options, $setting);
        }
    }

    /**
    * After render callback.
    * @param mixed $options
    * @param mixed $setting
    */
    public function afterRenderCallback($options, $setting) { 
        if ($c = $this->m_afterRender){            
            $c($options, $setting);
        }
    }
}