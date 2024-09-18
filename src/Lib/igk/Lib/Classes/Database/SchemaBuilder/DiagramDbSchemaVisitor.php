<?php
// @author: C.A.D. BONDJE DOUE
// @filename: d.php
// @date: 20220531 13:34:45
// @desc: 

namespace IGK\Database\SchemaBuilder;
 
use IGK\Database\DbSchemas;
use IGK\System\Html\XML\XmlNode;

/**
 * diagram schema visitor
 * @package igk\db\schemaBuilder
 */
class DiagramDbSchemaVisitor extends DiagramVisitor{
    private $visitor_items = [];
   
    public function start():?string{
        $this->visitor_items = [];
        return  "<".IGK_SCHEMA_TAGNAME.">";
    }
    public function complete():?string{
        return  "</".IGK_SCHEMA_TAGNAME.">";
    }
    public function visitDiagramEntity($entity, $diagram=null){
        $o = "";
        $n = new XmlNode(DbSchemas::DATA_DEFINITION);
        $tb = $entity->getName();
        $key = $n["TableName"] = $diagram? $diagram->getTableName($tb) : $tb;
        $this->visitor_items[$key] = $n;
        $n["Description"] = $entity->getDescription();
        if($p = $entity->getProperties()){
            foreach($p as $l){
                $ul = $n->add(DbSchemas::COLUMN_TAG);
                $r = (array)$l;
                if (!DiagramEntityColumnInfo::SupportTypeLength($r["clType"])){
                    unset($r["clTypeLength"]);
                } 
                $ul->setAttributes($r); 
            }
        }
        $o = $n->render();       
        return $o;
    }
}
