<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DiagramRelation.php
// @date: 20220531 16:28:07
// @desc: 
namespace IGK\Database\SchemaBuilder;
/**
 * 
 * @package igk\db\schemaBuilder
 */
class DiagramRelation extends DiagramPropertiesHost{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $name;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $sc;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $dc;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $startType;

    /**
    * auto generate doc.
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
    * auto generate doc.
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