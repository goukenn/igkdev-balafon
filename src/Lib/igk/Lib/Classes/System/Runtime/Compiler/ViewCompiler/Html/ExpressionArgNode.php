<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionArgNode.php
// @date: 20221018 11:48:48
namespace IGK\System\Runtime\Compiler\ViewCompiler\Html;

use IGK\System\Html\Dom\HtmlNode;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\Html
*/
class ExpressionArgNode extends HtmlNode{
    public function __construct(string $tagname)
    {
        parent::__construct($tagname);
    }
    public function getTagName($options = null)
    { 
        return  $this->tagname;
    }
}