<?php
// @file: IGKXmlDocument.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\XML;

use IGK\System\Html\HtmlRenderer;

class XmlDocument extends XmlNode{
    private $_docType;
    ///<summary>Represente __construct function</summary>
    ///<param name="tag"></param>
    ///<param name="docType" default="null"></param>
    public function __construct($tag, $docType=null){
        parent::__construct($tag);
        $this->_docType=$docType;
    }
    ///<summary>Represente render function</summary>
    ///<param name="option" default="null"></param>
    public function render($option=null){
        $sb=igk_xml_header().PHP_EOL;
        if($this->_docType)
            $sb .= "<!DOCTYPE ".$this->_docType. ">".PHP_EOL;
        $this->NoOverride=1;
        $min = HtmlRenderer::Render($this, $option);
        unset($this->NoOverride);
        return $sb.$min;
    }
}
