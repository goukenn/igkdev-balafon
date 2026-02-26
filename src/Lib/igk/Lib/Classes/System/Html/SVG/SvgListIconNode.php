<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SvgListIconNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\SVG;
use IGK\System\Html\Dom\HtmlNode;
use IGKEvents;
/**
 * list icon node
 * @package IGK\System\Html\SVG
 */
class SvgListIconNode extends HtmlNode{

    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname = "div";

    /**
    * .ctr
    * @param mixed $name
    */
    public function __construct($name){
        parent::__construct();
        $this["class"] = "igk-svg-lst-i";
        $this["igk:svg-name"] = $name; 
    }

    /**
    * Accept render.
    * @param null|mixed $options
    * @return bool
    */
    protected function _acceptRender($options = null):bool
    { 
        if (parent::_acceptRender($options)){
            SvgRenderer::AcceptRenderList($options);
            $cl = null;
            $name = $this["igk:svg-name"]; 
            if ($path = SvgRenderer::GetPath($name, $cl)){
                SvgRenderer::$RegisterPath[$name] = $path;  
                if ($cl){
                    $b = igk_css_str2class_name($name);
                    $this->setClass("+".$cl ." +".$b );
                }
                return true;
            }
        }
        return false;
    }
}