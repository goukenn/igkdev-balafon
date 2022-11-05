<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlTemplateReaderDataBinding.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Html;

use IGK\Controllers\BaseController;
use IGK\Helper\Activator;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\Dom\HtmlNoTagNode;
use IGKException;

/**
 * represent loop data binding
 */
class HtmlTemplateReaderDataBinding{
    var $node;
    var $data;
    var $ctrl;
    var $source; 

    public function __construct(HtmlItemBase $node, string $source, ?BaseController $ctrl=null, ?array $data=null){
        $this->node = $node;
        $this->data = $data;
        $this->ctrl = $ctrl;
        $this->source = $source; 
    }
    /**
     * treat binding
     * @return string 
     * @throws IGKException 
     */
    public function treat(){
        $data = $this->data;
        $script_obj = & $this->m_script;       
        $ctrl = $this->ctrl;
        $cnode = $this->node;
        $engine = ""; 
        // backup attribute
        $bck_attribs = $cnode->getAttributes()->to_array();
        $script_obj = igk_html_databinding_getobjforscripting($ctrl);
        $v_gtag = $cnode->getCanRenderTag() ? $cnode->tagName : null;         
        foreach($data as $key=>$raw){
            $c= $this->_treat_content(["type"=>"loop", "key"=>$key, "value"=>$raw, "raw"=>$raw, "transformToEval"=>true]);             
            if($c){
                $attribs = $cnode->getAttributes()->to_array();  
                $engine .= trim(igk_html_wtag($v_gtag, $c->render(), $attribs,
                    $cnode->closeTag()
                )); 
            }
        }
        // restore attribute 
        $cnode->clearAttributes();
        $cnode->setAttributes($bck_attribs);
        return $engine;
    }
     /**
      * 
      * @param array $data 
      * @return HtmlNoTagNode 
      */
    private function _treat_content(array $data){
        $ldcontext = Activator::CreateNewInstance( HtmlBindingContextOptions::class, (object)array_merge([
            "_data_type"=>__FUNCTION__,
            "ctrl"=>$this->ctrl, 
            "engineNode"=>$this->node
        ], $data));
        $target = igk_create_notagnode();
        $target->Load($this->source, $ldcontext);
        return $target;
    }
}