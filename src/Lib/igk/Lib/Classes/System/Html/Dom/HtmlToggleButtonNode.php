<?php

namespace IGK\System\Html\Dom;

class HtmlToggleButtonNode extends HtmlNode{
    protected $tagname ="button";

    protected function initialize()
    {
        $this["class"]="igk-toggle-button";
        $this["igk-toggle-button"]=true;
        $this["igk-toggle-state"]="collapse";
    }
    public function addBar($c=1){
        $this->clearChilds();
        for($i=0; $i < $c; $i++)
            $this->add("span")->setClass("igk-iconbar dispb");
        return $this;
    }
}