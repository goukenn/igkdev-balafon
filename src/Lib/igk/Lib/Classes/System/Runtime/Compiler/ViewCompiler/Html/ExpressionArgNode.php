<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionArgNode.php
// @date: 20221018 11:48:48
namespace IGK\System\Runtime\Compiler\ViewCompiler\Html;
use IGK\System\Html\Dom\HtmlNode;
/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler\Html
*/
class ExpressionArgNode extends HtmlNode{
    /**
    * .ctr
    * @param string $tagname
    */
    public function __construct(string $tagname)
    {
        parent::__construct($tagname);
    }
    /**
    * Returns Tag Name.
    * @param null|mixed $options
    */
    public function getTagName($options = null)
    { 
        return  $this->tagname;
    }
}