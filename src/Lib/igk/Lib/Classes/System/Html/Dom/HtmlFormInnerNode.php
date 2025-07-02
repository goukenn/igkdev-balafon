<?php
// @file: IGKHtmlFormInner.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;


final class HtmlFormInnerNode extends HtmlNode{
    private $m_form;
    public function __construct($form){
        parent::__construct( "igk:form-inner");
        $this->m_form=$form;
    }
    public function getCanRenderTag()
    {
        return false;
    }
}
