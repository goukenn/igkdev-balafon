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
    var $name;
    var $sc;
    var $dc;
    var $startType;
    var $endType;
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
    public function getDefinition(){
        return sprintf("%s", implode(",", array_filter([
            $this->sc->getName(),
            $this->dc->getName(),
            $this->startType,
            $this->endType,
        ])));
    }
  
}