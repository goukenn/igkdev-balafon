<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlWidgetNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
/**
 * represent a web widget
 * @package IGK\System\Html\Dom
 */
class HtmlWidgetNode extends HtmlNode{
    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname = "div";
    /**
    * .ctr
    * @param null|mixed $tagname
    */
    public function __construct($tagname=null){
        parent::__construct($tagname);
    }
    /**
    * Initializes.
    */
    protected function initialize()
    {
        parent::initialize();
        $this["class"] = "widget";
    }
}