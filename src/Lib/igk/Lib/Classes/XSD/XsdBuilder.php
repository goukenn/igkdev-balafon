<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XsdBuilder.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\XSD;
use ArrayAccess;
use Exception;
/**
* auto generate doc.
* @package IGK\XSD
*/
/**
* auto generate doc.
* @package IGK\XSD
*/
class XsdBuilder extends XsdElement implements ArrayAccess{
    /**
    * Constant: schema.
    * @var mixed
    */
    const SCHEMA = "http://www.w3.org/2001/XMLSchema";
    /**
    * Constant: any attribute.
    * @var mixed
    */
    const ANY_ATTRIBUTE = -1; 
    /**
    * Constant: any attribute lax.
    * @var mixed
    */
    const ANY_ATTRIBUTE_LAX = -2; 
    /**
    * Constant: any attribute skip.
    * @var mixed
    */
    const ANY_ATTRIBUTE_SKIP = -3; 
    /**
    * Property: notation.
    * @var mixed
    */
    private $m_notation;
    /**
     * Create a group element
     * @param mixed $name group name
     * @param mixed $items elements sequence
     * @param mixed|null $attributes for reference  
     * @return XsdGroup 
     */
    public function CreateGroup($name, $items, $attributes=null, $type="sequence"): XsdGroup{
        if (!in_array($type, explode("|", "choice|sequence|all"))){
            die("type not valie");
        } 
        $n = $this->_buildGroup($name, $items, "xs:group", "xs:all");
        $g = new XsdGroup($this, $n);        
        $g->name = $name;
        $g->attributes = $attributes;
        return $g;
    }
    /**
    * Creates Choice.
    * @param mixed $name
    * @param mixed $items
    * @param null|mixed $attributes
    * @return XsdChoice
    */
    public function CreateChoice($name, $items, $attributes=null): XsdChoice{
        $n = $this->_buildGroup($name, $items, "xs:choice");        
        $g = new XsdChoice($this, $n);        
        $g->name = $name;
        $g->attributes = $attributes;
        return $g;
    }
    /**
    * .ctr
    */
    public function __construct(){
        $this->m_node = igk_create_xmlnode("xs:schema");
        $this->m_node["xmlns:xs"] = self::SCHEMA;
    } 
    /**
     * set the notation
     * @param mixed $appinfo 
     * @param string $documentation 
     * @return $this 
     */
    public function setNotation($appinfo, $documentation=""){
        $notation = $this->m_notation ?? $this->m_node->add("xs:annotation");
        $notation->clearChilds();
        $notation->add("xs:appinfo")->Content = $appinfo;
        $notation->add("xs:documentation")->Content = $documentation;
        $this->m_notation = $notation;
        return $this;
    }
    /**
    * auto generate doc.
    * @return mixed
    */
    public function render(){
        return $this->m_node->render();
    }
    /**
    * add type element
    * @param mixed $name
    * @return XsdElementBuilder
    */
    public function addElement($name):XsdElementBuilder{
        $n = $this->m_node->add("xs:element")->setAttribute("name", $name);
        return XsdElementBuilder::Create($n, $this);
    }
    /**
     * add a globat attribute definition
     * @param mixed $name 
     * @param mixed $type 
     * @return XsdAttributeBuilder 
     */
    public function addAttribute($name, $type):XsdAttributeBuilder{
        $n = $this->m_node->add("xs:attribute")
        ->setAttribute("name", $name)
        ->setAttribute("type", $type)
        ;
        return XsdAttributeBuilder::Create($n, $this);
    }
    /**
    * auto generate doc.
    * @param mixed $name
    * @param mixed $items
    * @param mixed $tag
    * @param mixed $itemTag
    * @return mixed
    */
    private function _buildGroup($name, $items, $tag="xs:group", $itemTag="xs:sequence"){
        $e = $this->m_node->add($tag)->setAttribute("name", $name);
        if ($items){
            $t = XsdBuilderUtility::BuildSequence($e, $items, $itemTag);
        }
        return $e;
    }
    /**
    * Adds Group Element.
    * @param mixed $name
    * @param mixed $items
    */
    public function addGroupElement($name, $items){
        $this->_buildGroup($name, $items);
        return $this;
    }
    /**
    * Adds Group Attributes.
    * @param mixed $name
    * @param mixed $items
    */
    public function addGroupAttributes($name, $items){
        $e = $this->m_node->add("xs:attributeGroup")->setAttribute("name", $name);
        if ($items){
            foreach($items as $k=>$v){
                XsdBuilderUtility::AddSequenceElement($e, $k, $v, "xs:attribute");  
            }
        }
        return $this;
    }
    /**
    * Adds Enum Element.
    * @param mixed $name
    * @param mixed $items
    */
    public function addEnumElement($name, $items){
        $e = $this->m_node->add("xs:element")->setAttribute("name", $name);
        $res = $e->add("xs:simpleType")->add("xs:restriction");
        $res->setAttribute("base", XsdTypes::TSTRING);
        foreach($items as $k){
            $res->add("xs:enumeration")->setAttribute("value", $k);
        }
        return $this;
    }
    /**
    * Adds Enum Type.
    * @param mixed $name
    * @param mixed $items
    */
    public function addEnumType($name, $items){
        $e = $this->m_node->add("xs:simpleType")->setAttribute("name", $name);
        $res = $e->add("xs:restriction");
        $res->setAttribute("base", XsdTypes::TSTRING);
        foreach($items as $k){
            $res->add("xs:enumeration")->setAttribute("value", $k);
        }
        return $this;
    }
    /**
    * Adds Pattern Element.
    * @param mixed $name
    * @param mixed $pattern
    */
    public function addPatternElement($name, $pattern){
        $e = $this->m_node->add("xs:element")->setAttribute("name", $name);
        $res = $e->add("xs:simpleType")->add("xs:restriction");
        $res->setAttribute("base", XsdTypes::TSTRING);        
        $res->add("xs:pattern")->setAttribute("value", $pattern);        
        return $this;
    }
    /**
    * auto generate doc.
    * @param mixed $name
    * @param mixed $type white space type
    * @return $this
    */
    public function addWhiteSpaceElement($name, $type){
        $e = $this->m_node->add("xs:element")->setAttribute("name", $name);
        $res = $e->add("xs:simpleType")->add("xs:restriction");
        $res->setAttribute("base", XsdTypes::TSTRING);        
        $res->add("xs:whiteSpace")->setAttribute("value", $type);        
        return $this;
    }
    /**
    * Adds Length Restriction Element.
    * @param mixed $name
    * @param mixed $minLength
    * @param mixed $maxLength
    */
    public function addLengthRestrictionElement($name, $minLength, $maxLength){
        $e = $this->m_node->add("xs:element")->setAttribute("name", $name);
        $res = $e->add("xs:simpleType")->add("xs:restriction");
        $res->setAttribute("base", XsdTypes::TSTRING);        
        $res->add("xs:minLength")->setAttribute("value", $minLength);        
        $res->add("xs:maxLength")->setAttribute("value", $maxLength);        
        return $this;
    }
    /**
    * define complex type
    * @param mixed $name
    * @param mixed|array $sequences
    * @param mixed $attributes
    * @param mixed $ctype
    * @return XsdBuilder
    */
    public function addComplexTypeElement($name, $sequences = [], $attributes =null, $ctype=
    XsdBuilderUtility::SEQUENCE): XsdBuilder{
        $e = XsdBuilderUtility::BuildComplexType($this->m_node, $sequences, $ctype);
        $e->setAttribute("name", $name);
        if ($attributes){
            if (!XsdBuilderUtility::BindAnyAttribute($e, $attributes)){                
                foreach($attributes as $k=>$c){
                    XsdBuilderUtility::AddSequenceElement($e, $k, $c, "xs:attribute");
                }
            }
        }
        return $this;
    }
    /**
    * Adds Attribute Only Complex Type Element.
    * @param mixed $name
    * @param mixed $attributes
    * @return XsdBuilder
    */
    public function addAttributeOnlyComplexTypeElement($name, $attributes = []): XsdBuilder{
        $e = $this->m_node->add("xs:complexType")->setAttribute("name", $name);
        if ($attributes){
            if (!XsdBuilderUtility::BindAnyAttribute($e, $attributes)){                
                foreach($attributes as $k=>$c){
                    XsdBuilderUtility::AddSequenceElement($e, $k, $c, "xs:attribute");                
                }
            }
        }
        return $this;
    }
}