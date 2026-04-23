<?php
// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
namespace IGK\System\Html\Dom;
use IGK\System\Html\XML\XmlComment;

/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
class HtmlCommentNode extends XmlComment{
    /**
    * .ctr
    * @param null|string $content
    */
    public function __construct(?string $content=null){
        parent::__construct();
        $this->setContent($content);
    }
}