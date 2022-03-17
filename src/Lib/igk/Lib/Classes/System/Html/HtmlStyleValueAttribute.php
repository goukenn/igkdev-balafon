<?php
// @file: IGKHtmlStyleValueAttribute.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;

use IGK\System\Html\Css\CssStyle;
use IGK\System\Html\Dom\HtmlCssValueAttribute;

final class HtmlStyleValueAttribute extends HtmlAttributeValue{
    private $m_o, $m_v;
    ///<summary></summary>
    ///<param name="target"></param>
    public function __construct($target){
        $this->m_o=$target;
    }
    ///<summary></summary>
    public function __sleep(){
        if(empty($this->m_v)){
            return array();
        }
        return array("m_v", "m_o");
    }
    public function __debugInfo()
    {
        return [];
    }
    public function __toString()
    {
        return $this->getValue();
    }
    ///<summary></summary>
    function __wakeup(){    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function getValue($options=null){
        $opt=IGK_STR_EMPTY;
        if(igk_xml_is_mailoptions($options)){
            $p=$this->m_o["class"];
            $style=new CssStyle();
            $s=trim($p ? $p->EvalClassStyle(): IGK_STR_EMPTY);
            if(!empty($s))
                $style->Load($s, 1, $p);
            $opt .= igk_css_get_style_from_map($this->m_target, $options, $style);
        }
        if(!empty($opt) && !empty($this->m_v))
            $opt .= " ";
            if (is_object($this->m_v)){
                $opt .= $this->m_v->getValue($options);
            } else {
                $opt=$opt.$this->m_v;
            }
        return empty($opt) ? null: $opt;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setValue($value){
        if(($value == null) || is_string($value) || ($value instanceof HtmlCssValueAttribute))
            $this->m_v=$value;
        else{
            igk_die("no value allowed ".$value. " target :".get_class($this->m_target));
        }
    }
}
