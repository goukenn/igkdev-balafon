<?php
// @file: IGKXmlCDATA.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\XML;

/**
* Xml cdata.
* @package IGK\System\Html\XML
*/
final class XmlCDATA extends XmlNode{
    /**
     * Constructor.
     */
    public function __construct(){
        parent::__construct("igk:cdata");
    }
    /**
     * Indicate that this node cannot have child nodes.
     * @return bool
     */
    function getCanAddChilds()
    {
        return false;
    }
    /**
     * Indicate that this node does not render its wrapping tag.
     * @return bool
     */
    public function getCanRenderTag(){
        return false;
    }
    /**
     * Render the node content wrapped in a CDATA section.
     * @param mixed $options Optional rendering options.
     * @return string
     */
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