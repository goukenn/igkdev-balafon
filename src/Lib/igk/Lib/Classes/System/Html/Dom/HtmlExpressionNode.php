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
    var $setting;
    ///<summary></summary>
    ///<param name="args" default="null"></param>
    ///<param name="ctrl" default="null"></param>
    /**
    * 
    * @param mixed $args the default value is null
    * @param mixed $ctrl the default value is null
    */
    public function __construct($args=null, $ctrl=null){
        parent::__construct("igk:expression-node");
        $this->raw=$args;
        $this->ctrl=$ctrl;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getIsRenderTagName(){
        return false;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    /**
    * 
    * @param mixed $options the default value is null
    */
    public function render($options=null){
        $script_obj=igk_html_databinding_getobjforscripting($this->ctrl);
        $_e=html_entity_decode($this["expression"]);
        $shift=0;
        if($_e[0] != "@"){
            if($script_obj->Count() > 1){
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
        return $sout;
    }
}