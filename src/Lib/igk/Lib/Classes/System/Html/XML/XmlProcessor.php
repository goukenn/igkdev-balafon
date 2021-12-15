<?php
// @file: IGKXmlProcessor.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\XML;

final class XmlProcessor extends XmlNode{
    ///<summary></summary>
    ///<param name="type"></param>
    public function __construct($type){
        parent::__construct($type);
    }
    ///<summary></summary>
    public function getCanAddChild(){
        return false;
    }
    ///<summary></summary>
    public function getCanRenderTag(){
        return false;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function render($options=null){
        $c="<?".$this->TagName." ";
        $c .= $this->getAttributeString(null);
        $c .= "?>";
        return $c;
    }
}
