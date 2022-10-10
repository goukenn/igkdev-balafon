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

use IGK\System\Html\HtmlRenderer;

final class XmlProcessor extends XmlNode{
    ///<summary></summary>
    ///<param name="type"></param>
    /**
     * 
     * @param mixed $type processor type 
     * @return void 
     */
    public function __construct(string $type="xml"){
        parent::__construct($type);
    }
    ///<summary></summary>
    public function getCanAddChilds(){        
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
        $c .= HtmlRenderer::GetAttributeString($this, $options);
        $c .= "?>";
        return $c;
    }
}
