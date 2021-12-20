<?php
// @file: IGKFormatGetValueString.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;
 
use IGKObject;

/** @package IGK\System\Html */
final class HtmlFormatGetValueString extends IGKObject implements IHtmlGetValue{
    private $m_member, $m_obj;
    ///<summary></summary>
    private function __construct(){    }
    ///<summary>display value</summary>
    public function __toString(){
        return "IGKFormatGetValueString::". $this->getValue();
    }
    ///<summary></summary>
    ///<param name="obj"></param>
    ///<param name="property"></param>
    public static function Create($obj, $property){
        if(!is_object($obj))
            return null;
        $out=new self();
        $out->m_obj=$obj;
        $out->m_member=$property;
        return $out;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function getValue($options=null){
        $c=$this->m_member;
        $v=$this->m_obj->$c;
        return HtmlRenderer::GetValue($v, $options);
    }
}
