<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DiagramRelation.php
// @date: 20220531 16:28:07
// @desc: 
namespace IGK\Database\SchemaBuilder;

/**
* auto generate doc.
* @package igk\db\schemaBuilder
*/
class DiagramRelation extends DiagramPropertiesHost{
    /**
    * Name of name.
    * @var mixed
    */
    var $name;
    /**
    * Property: sc.
    * @var mixed
    */
    var $sc;
    /**
    * Property: dc.
    * @var mixed
    */
    var $dc;
    /**
    * Type of start type.
    * @var mixed
    */
    var $startType;
    /**
    * Type of end type.
    * @var mixed
    */
    var $endType;
    /**
    * .ctr
    * @param string $relationName
    * @param mixed $sourceEntity
    * @param mixed $endEntity
    * @param mixed $startType
    * @param null|mixed $endType
    */
    public function __construct(string $relationName, $sourceEntity, $endEntity, $startType, $endType=null)
    {
        if (is_null($sourceEntity)){
            die("sourceEntity is null");
        }
        if (is_null($endEntity)){
            die("endEntity is null");
        }
        $this->name = $relationName;
        $this->sc = $sourceEntity;
        $this->dc = $endEntity;
        $this->startType = $startType;
        $this->endType = $endType;
        $this->m_properties = [];
    }
    /**
    * Returns Definition.
    */
    public function getDefinition(){
        return sprintf("%s", implode(",", array_filter([
            $this->sc->getName(),
            $this->dc->getName(),
            $this->startType,
            $this->endType,
        ])));
    }
}