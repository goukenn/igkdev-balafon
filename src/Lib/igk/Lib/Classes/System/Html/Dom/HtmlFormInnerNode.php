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
/**
* Html form inner node.
* @package IGK\System\Html\Dom
*/
final class HtmlFormInnerNode extends HtmlNode{
    /**
    * Property: form.
    * @var mixed
    */
    private $m_form;
    /**
     * Constructor.
     * @param mixed $form The form node this inner node belongs to.
     */
    public function __construct($form){
        parent::__construct( "igk:form-inner");
        $this->m_form=$form;
    }
    /**
     * Indicates that this node does not render its tag.
     * @return bool
     */
    public function getCanRenderTag()
    {
        return false;
    }
}