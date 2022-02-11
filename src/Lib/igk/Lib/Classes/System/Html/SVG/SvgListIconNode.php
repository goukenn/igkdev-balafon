<?php

namespace IGK\System\Html\SVG;

use IGK\System\Html\Dom\HtmlNode;
use IGKEvents;

class SvgListIconNode extends HtmlNode{
    protected $tagname = "div";
    public function __construct($name){
        parent::__construct();
        $this["class"] = "igk-svg-lst-i";
        $this["igk:svg-name"] = $name; 
    }

    protected function __AcceptRender($options = null)
    { 
        if (parent::__AcceptRender($options)){
            SvgRenderer::AcceptRenderList($options);
            $cl = null;
            if ($path = SvgRenderer::GetPath($name = $this["igk:svg-name"], $cl)){
                SvgRenderer::$RegisterPath[$name] = $path;
                //  igk_wln_e("data ::: ", $cl);
                if ($cl){
                    $this->setClass("+".$cl);
                }
                return true;
            } 
        }
        return false;
    }
}