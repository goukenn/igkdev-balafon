<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SvgListIconNode.php
// @date: 20220803 13:48:56
// @desc: 


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

    protected function _acceptRender($options = null):bool
    { 
        if (parent::_acceptRender($options)){
            SvgRenderer::AcceptRenderList($options);
            $cl = null;
            if ($path = SvgRenderer::GetPath($name = $this["igk:svg-name"], $cl)){
                SvgRenderer::$RegisterPath[$name] = $path; 
                if ($cl){
                    $this->setClass("+".$cl ." +".$name );
                }
                return true;
            } 
        }
        return false;
    }
}