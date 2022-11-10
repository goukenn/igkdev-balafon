<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlCodeNode.php
// @date: 20220706 16:12:34
// @desc: code node


namespace IGK\System\Html\Dom;

class HtmlCodeNode extends HtmlNode{
    protected $tagname = "code";

    public function setContent($v){
        $this->content = $v;
        return $this;
    }
}