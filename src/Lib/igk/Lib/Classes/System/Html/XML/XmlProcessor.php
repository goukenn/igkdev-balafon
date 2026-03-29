<?php
// @file: IGKXmlProcessor.php
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
* Xml processor.
* @package IGK\System\Html\XML
*/
final class XmlProcessor extends XmlNode{
    /**
    * auto generate doc.
    * @param mixed $type processor type
    * @return void
    */
    public function __construct(string $type="xml"){
        parent::__construct($type);
    }
    /**
    * Returns Can Add Childs.
    */
    public function getCanAddChilds(){        
        return false;
    }
    /**
    * Returns Can Render Tag.
    */
    public function getCanRenderTag(){
        return false;
    }
    /**
    * Renders.
    * @param null|mixed $options
    */
    public function render($options=null){
        $c="<?".$this->TagName." ";
        $c .= HtmlRenderer::GetAttributeString($this, $options);
        $c .= "?>";
        return $c;
    }
}