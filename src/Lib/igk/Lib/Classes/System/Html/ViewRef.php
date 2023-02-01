<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewRef.php
// @date: 20221231 16:35:05
namespace IGK\System\Html;


///<summary></summary>
/**
* 
* @package IGK\System\Html
*/
class ViewRef implements IHtmlGetValue{
    var $data;
    public function __construct($data){
        $this->data = $data;
    }
    public function getValue($options = null) {
        // + evaluate expression        
        if ($options && ($options->renderingContext == RenderingContext::TEMPLATE)){
            $g = new ViewRefAttribute;
            $g->data = $this->data;
            return $g;
        }        
        if (igk_environment()->isDev()){
            igk_trace();
            igk_wln_e("not in rendering context ....", $options->renderingContext);
        }
        return $this->data;
    }
    public function __toString(){
        return $this->data;
    }
}