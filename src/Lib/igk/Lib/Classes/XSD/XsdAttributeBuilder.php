<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XsdAttributeBuilder.php
// @date: 20220803 13:48:54
// @desc: 



namespace IGK\XSD;

use IGKXmlNode;

class XsdAttributeBuilder extends XsdElement{
    private function __construct()
    {
        
    }
    public static function Create(IGKXmlNode $node, XsdBuilder $builder)
    {
        $n = new self;
        $n->m_node = $node;
        $n->m_builder = $builder;
        return $n;
    }
    public function setRequire($b){
        if ($b)
            $this->m_node["use"] = "require";
        else{
            $this->m_node["use"] = null;
        }
    }
}