<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XsdAttributeBuilder.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\XSD;
use IGKXmlNode;

/**
* auto generate doc.
* @package IGK\XSD
*/
class XsdAttributeBuilder extends XsdElement{
    private function __construct()
    {
    }

    /**
    * auto generate doc.
    * @param IGKXmlNode $node
    * @param XsdBuilder $builder
    */
    public static function Create(IGKXmlNode $node, XsdBuilder $builder)
    {
        $n = new self;
        $n->m_node = $node;
        $n->m_builder = $builder;
        return $n;
    }

    /**
    * auto generate doc.
    * @param mixed $b
    */
    public function setRequire($b){
        if ($b)
            $this->m_node["use"] = "require";
        else{
            $this->m_node["use"] = null;
        }
    }
}