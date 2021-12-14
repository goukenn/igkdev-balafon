<?php
// @file: IGKXmlCDATA.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\XML;
 

final class XmlCDATA extends XmlNode{
    ///<summary></summary>
    public function __construct(){
        parent::__construct("igk:cdata");
    }
    ///<summary></summary>
    public function getCanAddChild(){
        return false;
    }
    ///<summary></summary>
    public function getIsRenderTagName(){
        return false;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function render($options=null){
        $c="<![CDATA[";
        $s=$this->Content;
        if(is_string($s))
            $c .= $s;
        else if(is_object($s) && (method_exists($s, "getValue")))
            $c .= $s->getValue();
        $c .= "]]>";
        return $c;
    }
}
