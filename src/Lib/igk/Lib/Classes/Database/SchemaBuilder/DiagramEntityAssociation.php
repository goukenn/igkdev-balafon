<?php

// @author: C.A.D. BONDJE DOUE
// @filename: DiagramEntityAssociation.php
// @date: 20220531 16:27:01
// @desc: 

namespace IGK\Database\SchemaBuilder;

use IGK\Database\DbColumnInfo;
use IGK\Helper\Activator;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKException;
use ReflectionException;

/**
 * entity association diagram builder. on render it will render a md file by default
 */
class DiagramEntityAssociation implements IDiagramSchemaBuilder{
    private $m_entities;
    private $m_relations;
    /**
     * get table prefix
     * @var mixed
     */
    var $table_prefix;
    /**
     * 
     * @var ?string database name
     */
    var $db_name;
    public function __construct(){
        $this->m_entities = [];
        $this->m_relations = [];
    }

    public function getTablePrefix(): string { 
        return $this->table_prefix;
    }
    public function getEntityKeys(){
        return array_keys($this->m_entities);
    }
    /**
     * addd prefix to table
     * @param string $table 
     * @return string 
     */
    public function getPrefixTable(string $table): string{
        return sprintf("%s%s", $this->table_prefix, $table);
    }
    /**
     * render schema 
     * @param null|DiagramVisitor $visitor 
     * @return string 
     * @throws IGKException 
     */
    public function render(?DiagramVisitor $visitor = null){
        $o = "";
        $visitor = $visitor ?? new DiagramVisitor();
        $visiting = false;
        $o.= $visitor->start();
        $prefix = $this->table_prefix ?? ""; 
        $v_tab_key = $this->db_name ? "## ": "# "; // md table key depend if db 
        if ($this->db_name && ($visitor instanceof DiagramVisitor)){
            $o.="# ".$this->db_name ."\n";
        }
        foreach($this->m_entities as $k=>$v){
            if ($visitor->acceptVisit($v)){
                $o .= $visitor->visit($v, $this);
                $visiting = true;
            }else{
                $o .= $v_tab_key.$prefix.$k . "\n";
                $p = $v->getProperties();
                $o.= self::_RenderProperties($p, $visitor, $this);
            }          
        }
        if (!$visiting && count($this->m_relations)>0){
            $o .= "\n---- \n";
            $o .= "---- \n\n";

            foreach($this->m_relations as $r){
                // igk_wln_e($r);
                $o.= "# [".$r->name."]\n";
                $o.= "-- (".$r->getDefinition().")\n";
                if ($p = $r->getProperties()){
                    $o .= self::_RenderProperties($p, $visitor, $this);
                }
            }
        }
        $o.= $visitor->complete();
        return $o;
    }

    public function getTableInfo(string $name){
        $g = igk_getv($this->m_entities, $name);
        if (!$g){
            return null;
        }
        $clinfo = [];
        foreach($g->getProperties() as $k=>$v){ 
            $b = (array)$v;
            $clinfo[$v->clName] = Activator::CreateNewInstance(DbColumnInfo::class, $b);
        } 
        $tab = [
            "ColumnInfo"=>(object)$clinfo,
            "Description"=>""
        ];
        return $tab;
    }
    private static function _RenderProperties($p, $visitor, $diagram){
        $o = "";
        $v_key = $diagram->db_name ? "### ": "## ";
        foreach($p as $c){
            if ($visitor->acceptVisit($c)){
                igk_wln($c, $visitor);
                $o.= $visitor->visit($c);
            }
            else{
                $o .= $v_key.$c->clName."\n";
                $o .= sprintf("%s",$c->clType);
                if ($c::SupportTypeLength($c->clType))
                    $o .= sprintf("(%s)", $c->clTypeLength);
                $o .="\n";
                if ($c->clIsUnique)
                    $o.="unique\n";
                if ($c->clAutoIncrement)
                    $o.="autoincrement\n";
                if ($c->clDefault)
                    $o.=sprintf("default(%s)\n", $c->clDefault);
                if ($c->clDescription)
                    $o.=sprintf("description(%s)\n", $c->clDescription);

            }
        }
        return $o;
    }
    /**
     * get or generated entities
     * @return DiagramEntity
     */
    public function entity($name, ?string $desc=null, ?string $prefix=null) : IDiagramSchemaEntity{
        if (is_string($name)){
            if (isset($this->m_entities[$name])){
                return $this->m_entities[$name];
            }
            $e = new DiagramEntity($name, $prefix);
        }       
        $desc && $e->setDescription($desc); 
        $this->m_entities[$e->getName()] = $e;        
        return $e;
    }
    
    public function link(string $relationName, $sourceEntity, $endEntity, $startType, $endType=null){
        $sc = (is_string($sourceEntity)? igk_getv($this->m_entities, $sourceEntity) : 
            (in_array($sourceEntity, $this->m_relations)? 
            $sourceEntity : null)) ??  die("source not in array")  ;
        $dc = (is_string($endEntity)? igk_getv($this->m_entities, $endEntity) : 
            (in_array($endEntity, $this->m_relations) ? 
            $endEntity : null)) ?? die("destination not in array");

        $this->m_relations[$relationName] = $c = new DiagramRelation($relationName, $sc, $dc, $startType, $endType);
        return $c;
    }

    /**
     * load from data schema
     * @param mixed $loadSchemaObject 
     * @return DiagramEntityAssociation 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function LoadFromXMLSchema($loadSchemaObject){
        $o = new self;
        $o->table_prefix = "%prefix%";
        if ($loadSchemaObject->tables){
            // load entities
            foreach($loadSchemaObject->tables as $k=>$v){
                $e = $o->entity($v->defTableName);
                $e->addProperties($v->columnInfo);
            }
        }
        // if (isset($loadSchemaObject->Entries)){
        //     // load entries
        //     foreach($loadSchemaObject->Entries as $k=>$v){
            
        //     }
        // }
        return $o;
    }
}
