<?php
// @file: IGKFormatGetValueString.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;
use IGKObject;
/** @package IGK\System\Html */
final class HtmlFormatGetValueString extends IGKObject implements IHtmlGetValue{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_member, $m_obj;
    private function __construct(){    }

    /**
    * get string presentation.
    */
    public function __toString(){
        return "IGKFormatGetValueString::". $this->getValue();
    }

    /**
    * auto generate doc.
    * @param mixed $obj
    * @param mixed $property
    */
    public static function Create($obj, $property){
        if(!is_object($obj))
            return null;
        $out=new self();
        $out->m_obj=$obj;
        $out->m_member=$property;
        return $out;
    }

    /**
    * auto generate doc.
    * @param null|mixed $options
    */
    public function getValue($options=null){
        $c=$this->m_member;
        $v=$this->m_obj->$c;
        return HtmlRenderer::GetValue($v, $options);
    }
}