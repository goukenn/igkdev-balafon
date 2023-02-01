<?php

namespace IGK\System\Html\Dom;

// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021///<summary>Represente class: IGKHtmlExpressionNodeItem</summary>
/**
* Represente IGKHtmlExpressionNodeItem class
*/
class HtmlExpressionNode extends HtmlNode{
    var $ctrl;
    var $raw; 
    var $openerContext;
    
    ///<summary></summary>
    ///<param name="args" default="null"></param>
    ///<param name="ctrl" default="null"></param>
    /**
    * 
    * @param mixed $args the default value is null
    * @param mixed $ctrl the default value is null
    */
    public function __construct($args=null, $ctrl=null, $openerContext=null){
        parent::__construct(IGK_ENGINE_EXPRESSION_NODE);
        $this->raw=$args;
        $this->ctrl=$ctrl;
        $this->openerContext = $openerContext;
    }
    /**
     * render tag name
     * @return bool 
     */
    public function getCanRenderTag()
    { 
        return false;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    /**
    * 
    * @param mixed $options the default value is null
    */
    public function render($options=null){
        $src = $this["expression"];
        if (empty($src)){
            return "";
        }
        $script_obj=igk_html_databinding_getobjforscripting($this->ctrl);
       
        $sout = "";
        // if ($script_obj){
            $_e=html_entity_decode($src); 
            $shift=0;
            if($_e[0] != "@"){
                if($script_obj && ($script_obj->Count() > 1)){
                    $script_obj->shiftParent();
                    $shift=1;
                }
            }
            while($_e[0] == "@"){
                $_e=substr($_e, 1);
            }
            if(empty($_e=trim($_e))){
                return "";
            } 
            $sout=igk_html_databinding_treatresponse($_e, $this->ctrl, $this->raw, null);
            if($shift){
                $script_obj->resetShift();
            }
        // } 
        return $sout;
    }
}