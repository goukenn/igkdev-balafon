<?php
// @file: IGKXmlDocument.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\XML;
use IGK\System\Html\HtmlRenderer;

/**
* Xml document.
* @package IGK\System\Html\XML
*/
class XmlDocument extends XmlNode{

    /**
    * Type of doc type.
    * @var mixed
    */
    private $_docType;

    /**
    * .ctr
    * @param mixed $tag
    * @param null|mixed $docType
    */
    public function __construct($tag, $docType=null){
        parent::__construct($tag);
        $this->_docType=$docType;
    }

    /**
    * Renders.
    * @param null|mixed $option
    */

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