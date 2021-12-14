<?php
// @file: IGKHtmlAttribExpressionNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html;

use IGK\System\Html\Dom\XmlNode;

class HtmlAttribExpressionNode extends XmlNode{
    var $m_context, $node_args;
    ///<summary>Represente __construct function</summary>
    ///<param name="c"></param>
    ///<param name="context" default="null"></param>
    public function __construct($c, $context=null){
        if(!is_array($c)){
            igk_die("node args must be an array");
        }
        parent::__construct(IGK_ENGINE_ATTR_EXPRESSION_NODE);
        $this->node_args=$c;
        $this->m_context=$context;
    }
    ///<summary>Represente getCanAddChild function</summary>
    public function getCanAddChild(){
        return false;
    }
    ///<summary>Represente loadingComplete function</summary>
    protected function loadingComplete(){
        $r=$this->node_args;
        $m=$this->Attributes->to_array();
        $_p=[];
        $_g=explode("|", "*for|*visible");
        if(!$this->m_context){
            $context=array_merge(["ctrl"=>$r[2]], isset($r[3]) ? (array)$r[3]->getArgs(): []);
            $context["raw"]=$context["value"];
        }
        else
            $context=$this->m_context;
        $context=igk_get_attrib_raw_context($context);
        foreach($m as $k=>$t){
            if(in_array($k, $_g))
                continue;
            if($k[0] == "*"){
                $t=igk_template_get_piped_value($t, $context);
                $k=ltrim($k, "*");
            }
            $_p[$k]=$t;
        }
        if(count($_p) > 0){
            $r[0]->setAttributes($_p);
            $m=& $r[1];
            $m=igk_html_render_attribs($r[0]->getAttributes());
        }
        $this->node_args=null;
        $this->dispose();
    }
}
