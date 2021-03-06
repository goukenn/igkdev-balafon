<?php

namespace IGK\System\Html\Dom;

use IGK\Controllers\BaseController;

/**
 * represent a controller node
 * @package IGK\System\Html\Dom
 */
class HtmlCtrlNode extends HtmlNode {
    private $m_controller;
    public function __construct(BaseController $controller, $tagname=null)
    {
        parent::__construct($tagname);
        $this->m_controller = $controller;
        $div["igk-type"]="controller";
    }
    public function getIsVisible(){
        return $this->m_controller->getIsVisible();
    }
}