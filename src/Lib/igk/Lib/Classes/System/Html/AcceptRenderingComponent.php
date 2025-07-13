<?php
// @author: C.A.D. BONDJE DOUE
// @file: AcceptRenderingComponent.php
// @date: 20250408 15:50:23
namespace IGK\System\Html;
use Exception;
/**
* definition to render on component visibility
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE
*/
class AcceptRenderingComponent{
    var $guid;
    private $m_script;
    private $m_styles;
    private $m_injects;
    public function __construct(string $guid)
    {
        $this->guid = $guid;
    }
    public function script(){
        if (is_null($this->m_script)){
            $this->m_script = igk_create_node('script');
        }
        return $this->m_script;
    }
    /**
     * 
     * @param mixed $n 
     * @param mixed $options 
     * @return false|void 
     * @throws Exception 
     */
    public function __invoke($n, $options){
        if (!$n->acceptRender($options))
            return false;
        // accepter render 
        if ($doc = igk_getv($options, 'Document')){
            $l = $doc->getDocumentInjector();
            $l->register($this->guid, function(){
                return $this->m_script->render();
            });
        }
        return true;
    }
}