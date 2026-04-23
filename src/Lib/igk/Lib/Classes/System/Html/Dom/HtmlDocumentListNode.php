<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlDocumentListNode.php
// @date: 20220803 13:48:56
// @desc: document list node 
namespace IGK\System\Html\Dom;
use IGK\System\Html\Dom\HtmlNode;

/**
 * document list node helper 
 * @package IGK\System\Html\Dom
 */
class HtmlDocumentListNode extends HtmlNode{
    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname = "dl";
    /**
     * dt definition 
     * @var mixed
     */
    /**
    * auto generate doc.
    * @var mixed
    */
    protected function initialize()
    {
        parent::initialize();
    }
    /**
     * clear childs 
     * @return static
     */
    public function clearChilds()
    {
        parent::clearChilds();
        $this->initialize();
        return $this;
    }
    /**
    * auto generate doc.
    * @return bool
    */
}