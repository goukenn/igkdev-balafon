<?php
// @author: C.A.D. BONDJE DOUE
// @filename: GlobalScriptManagerHostNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
/**
* used to render global script
*/
final class GlobalScriptManagerHostNode extends HtmlNode{

    /**
    * auto generate doc.
    */
    public function __construct(){
        parent::__construct('igk:scripthostnode');
    }

    /**
    * auto generate doc.
    * @param mixed $options the default value is null
    */
    public function render($options=null){        
        $v= "";
        if(igk_xml_is_mailoptions($options)){
            $v .= "<!-- render script -->";                
        }
        return $v;
    }

    /**
    * Returns Can Render Tag.
    */
    public function getCanRenderTag()
    {
        return false;
    }
}