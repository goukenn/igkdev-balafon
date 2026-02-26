<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlStyleNode.php
// @date: 20220803 13:48:56
// @desc:
namespace IGK\System\Html\Dom;

/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
class HtmlStyleNode extends HtmlNode{

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $tagname = "style";
    /**
     * Constructor.
     */

    public function __construct(){
        parent::__construct();
        $this["type"] = "text/css";
    }
}
