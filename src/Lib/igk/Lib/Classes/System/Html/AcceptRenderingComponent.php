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

    /**
    * Identifier: guid.
    * @var mixed
    */
    var $guid;

    /**
    * Property: script.
    * @var mixed
    */
    private $m_script;

    /**
    * Property: styles.
    * @var mixed
    */
    private $m_styles;

    /**
    * Property: injects.
    * @var mixed
    */
    private $m_injects;

    /**
    * .ctr
    * @param string $guid
    */
    public function __construct(string $guid)
    {
        $this->guid = $guid;
    }

    /**
    * Script.
    */
    public function script(){
        if (is_null($this->m_script)){
            $this->m_script = igk_create_node('script');
        }
        return $this->m_script;
    }

    /**
    * auto generate doc.
    * @param mixed $options
    * @return false|void
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