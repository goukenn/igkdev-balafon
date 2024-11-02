<?php
// @author: C.A.D. BONDJE DOUE
// @file: ScopedNodeTrait.php
// @date: 20241016 13:35:45
namespace IGK\System\Html\Rendering\Traits;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Rendering\Trait
* @author C.A.D. BONDJE DOUE
*/
trait ScopedNodeTrait{
    private $m_beforeRender;
    private $m_afterRender;
       /**
     * set before callback listener 
     * @param ?callable $callback 
     * @return void 
     */
    public function beforeRender($callback){
        $this->m_beforeRender = $callback;
    }
    public function afterRender($callable){
        $this->m_afterRender = $callable;
    }

    public function beforeRenderCallback($options, $setting) {
        if ($c = $this->m_beforeRender){            
            $c($options, $setting);
        }
    }

    public function afterRenderCallback($options, $setting) { 
        if ($c = $this->m_afterRender){            
            $c($options, $setting);
        }
    }
}