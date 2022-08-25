<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlTemplateReaderDataBinding.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Html;

use IGK\Controllers\BaseController;
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
        $n_context=["scope"=>0, "contextlevel"=>1, "fname"=>"__memory__", "data"=>null];
        $n_options=(object)["Indent"=>0, "Depth"=>0, 
            "Context"=>"html", 
            "Source"=>$this->node,
            "RContext"=>$n_context, 
            "ctrl"=>$ctrl];
        // backup attribute
        $bck_attribs = $cnode->getAttributes()->to_array();


        $script_obj = igk_html_databinding_getobjforscripting($ctrl);
        $v_gtag = $cnode->getCanRenderTag() ? $cnode->tagName : null;        
        // $gb = igk_create_xmlnode("div"); 
        // $gb->load($this->source, HtmlContext::XML);              
        foreach($data as $key=>$raw){
            $c= $this->treat_content(["type"=>"loop", "key"=>$key, "value"=>$raw, "raw"=>$raw]); 
            if($c){
                $attribs = $cnode->getAttributes()->to_array();  
      
      
                $engine .= trim(igk_html_wtag($v_gtag, trim($c->render()), $attribs,
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
    private function treat_content(array $data){
        $ldcontext = (object)array_merge([
            "ctrl"=>$this->ctrl, 
            "engineNode"=>$this->node
        ], $data);
        $target = igk_create_notagnode();
        $target->Load($this->source, $ldcontext);
        return $target;
    }
}