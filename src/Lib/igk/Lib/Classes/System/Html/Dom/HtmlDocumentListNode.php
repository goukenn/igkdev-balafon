<?php

namespace IGK\System\Html\Dom;
use IGK\System\Html\Dom\HtmlNode;

class HtmlDocumentListNode extends HtmlNode{
    protected $tagname = "dl";

    var $dd;
    var $dt;

    protected function initialize()
    {
        parent::initialize();
        $this->dt = new HtmlNode("dt");
        $this->dd = new HtmlNode("dd");
        parent::_Add($this->dt, true);
        parent::_Add($this->dd, true);
    }
    public function clearChilds()
    {
        parent::clearChilds();
        $this->initialize();
    }
    public function getCanAddChilds()
    {
        return false;
    }
}