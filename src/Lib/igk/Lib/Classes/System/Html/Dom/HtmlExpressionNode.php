<?php
// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
namespace IGK\System\Html\Dom;
use IGK\System\Html\HtmlBindingRawTransform;
use IGK\System\Templates\BindingExpressionReader;
/**
* Represent IGKHtmlExpressionNodeItem class
*/
class HtmlExpressionNode extends HtmlNode{

    /**
    * Property: ctrl.
    * @var mixed
    */
    var $ctrl;

    /**
    * Property: raw.
    * @var mixed
    */
    var $raw;

    /**
    * Property: opener context.
    * @var mixed
    */
    var $openerContext;

    /**
    * auto generate doc.
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

    /**
    * auto generate doc.
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
            $exp_reader = new BindingExpressionReader;      
            if ($this->raw instanceof HtmlBindingRawTransform){
                $exp_reader->transformToEval = true;
            }
            $sout = $exp_reader->treatContent($_e, (object)['raw'=>$this->raw, 'ctrl'=>$this->ctrl]);
            //$sout=igk_html_databinding_treatresponse($_e, $this->ctrl, $this->raw, null);
            if($shift){
                $script_obj->resetShift();
            }
        // } 
        return $sout;
    }
}