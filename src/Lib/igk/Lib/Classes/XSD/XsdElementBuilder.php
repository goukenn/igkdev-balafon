<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XsdElementBuilder.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\XSD;
use ArrayAccess;
use IGKXmlNode;

/**
* Xsd element builder.
* @package IGK\XSD
*/
class XsdElementBuilder extends XsdElement 
{

    /**
    * Property: builder.
    * @var mixed
    */
    private $m_builder;

    /**
    * Property: defining.
    * @var mixed
    */
    private $_defining; 
    private function __construct()
    {
    }
    /**
     * @param IGKXmlNode $node 
     * @param XsdBuilder $builder 
     * @return XsdElementBuilder 
     */

    public static function Create(IGKXmlNode $node, XsdBuilder $builder)
    {
        $n = new XsdElementBuilder;
        $n->m_node = $node;
        $n->m_builder = $builder;
        return $n;
    }

    /**
    * Sets Attribute.
    * @param mixed $name
    * @param mixed $value
    */

    public function setAttribute($name, $value){
        $this->m_node->setAttribute($name, $value);
        return $this;
    }

    /**
    * Sets Default.
    * @param mixed $defaultvalue
    */

    public function setDefault($defaultvalue){
        if ($this->_defining) {
            throw new XsdBuilderException("type already defined");
        }
        if ($defaultvalue){
            $this->_defining = true;
            $this->m_node["default"] = $defaultvalue;
        }
        return $this;
    }

    /**
    * Sets Fixed.
    * @param mixed $defaultvalue
    */

    public function setFixed($defaultvalue){
        if ($this->_defining) {
            throw new XsdBuilderException("type already defined");
        }
        if ($defaultvalue){
            $this->_defining = true;
            $this->m_node["fixed"] = $defaultvalue;
        }
        return $this;
    }
    /**
     * 
     * @param array $defs 
     * @param mixed|null $attributes 
     * @return void 
     * @throws XsdBuilderException 
     */

    public function addComplexType(array $defs, $attributes=null, $type="sequence", $tattributes=null)
    {
        if (!in_array($type, explode("|","choice|sequence|all"))){
            igk_wln_e("not a valid complex type : ".$type);
        }
        if ($this->_defining) {
            throw new XsdBuilderException("type already defined");
        }
        $this->_defining = true;
        $b = XsdBuilderUtility::BuildComplexType($this->m_node, $defs, "xs:".$type, $tattributes);
        if ($attributes && count($attributes)){
            // $seq = $e->add("xs:sequence");
            foreach($attributes as $k=>$c){
                XsdBuilderUtility::AddSequenceElement($b, $k, $c, "xs:attribute");                
            }
        }
        return $this;
    }
}