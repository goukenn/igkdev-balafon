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
* auto generate doc.
* @package IGK\System\Html\XML
*/
final class XmlProcessor extends XmlNode{
    /**
     * 
     * @param mixed $type processor type 
     * @return void 
     */
    public function __construct(string $type="xml"){
        parent::__construct($type);
    }

    /**
    * auto generate doc.
    */
    public function getCanAddChilds(){        
        return false;
    }

    /**
    * auto generate doc.
    */
    public function getCanRenderTag(){
        return false;
    }

    /**
    * auto generate doc.
    * @param null|mixed $options
    */
    public function render($options=null){
        $c="<?".$this->TagName." ";
        $c .= HtmlRenderer::GetAttributeString($this, $options);
        $c .= "?>";
        return $c;
    }
}