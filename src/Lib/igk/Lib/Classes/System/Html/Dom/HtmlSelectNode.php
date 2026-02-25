<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlSelectNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
class HtmlSelectNode extends HtmlNode{    
    protected $tagname = "select";
    /**
     * Constructor.
     *
     * @param bool $autoremove Whether the node should be automatically removed.
     */
    public function __construct(bool $autoremove=true){
        parent::__construct();    
    }
}   