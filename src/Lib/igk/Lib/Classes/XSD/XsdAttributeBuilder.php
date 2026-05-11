<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XsdAttributeBuilder.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\XSD;
use IGKXmlNode;

/**
* Xsd attribute builder.
* @package IGK\XSD
*/
class XsdAttributeBuilder extends XsdElement{
    /**
    * .ctr
    * @return mixed
    */
    private function __construct()
    {
    }
    /**
    * Creates.
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
    * Sets Require.
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