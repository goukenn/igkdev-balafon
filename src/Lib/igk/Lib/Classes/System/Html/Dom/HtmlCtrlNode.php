<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlCtrlNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
use IGK\Controllers\BaseController;
/**
 * represent a controller node
 * @package IGK\System\Html\Dom
 */
class HtmlCtrlNode extends HtmlNode {

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_controller;

    /**
    * .ctr
    * @param BaseController $controller
    * @param null|mixed $tagname
    */
    public function __construct(BaseController $controller, $tagname=null)
    {
        parent::__construct($tagname);
        $this->m_controller = $controller;        
    }

    /**
    * auto generate doc.
    */
    protected function initialize()
    {
        $this['igk-type'] = 'controller';
    }

    /**
    * auto generate doc.
    */
    public function getIsVisible(){
        return $this->m_controller->getIsVisible();
    }
}