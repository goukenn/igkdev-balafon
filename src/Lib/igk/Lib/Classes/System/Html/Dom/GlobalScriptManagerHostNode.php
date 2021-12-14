<?php
namespace IGK\System\Html\Dom;

///<summary>used to render global script</summary>
/**
* used to render global script
*/
final class GlobalScriptManagerHostNode extends HtmlNode{
    ///<summary></summary>
    /**
    * 
    */
    public function __construct(){
        parent::__construct('igk:scripthostnode');
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    /**
    * 
    * @param mixed $options the default value is null
    */
    public function render($options=null){
        $v=igk_ob_get_func(function() use ($options){
            igk_app()->getDoc()->getScriptManager()->localScriptRenderCallback($options);
        });
        return $v;
    }
}