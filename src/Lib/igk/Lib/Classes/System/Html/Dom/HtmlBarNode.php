<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlBarNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
/**
* Html bar node.
* @package IGK\System\Html\Dom
*/
class HtmlBarNode extends HtmlNode{
    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname ="span";
    /**
    * Initializes.
    */
    protected function initialize()
    {   
        $this["class"] = "igk-bar";
    }
}