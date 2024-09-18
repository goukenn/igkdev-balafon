<?php


// @author: C.A.D. BONDJE DOUE
// @filename: DiagramHelper.php
// @date: 20220728 12:16:48
// @desc: diagram helper 

namespace IGK\Database\SchemaBuilder;
 
use IGK\System\Html\XML\XmlNode;
use IGKException;

abstract class DiagramHelper{
    /**
     * resolve loaded links
     * @param IDiagramLoadingDefinition $loadSchemaObject 
     * @param array<string, DiagramEntityColumnInfo> $links 
     * @return void 
     * @throws Exception 
     * @throws IGKException 
     */
    public static function ResolveLinks($loadSchemaObject, $links){

        foreach($links as $def){
            
            foreach($def as $column){
                $v_rtable = $column->clLinkType;
                $v_n = $column->clLinkColumn; 
                $cinfo = igk_getv($loadSchemaObject->tables, $v_rtable);
                if ($cinfo){
                    // table present in loading definition object 
                    if (!isset($cinfo->columnInfo[$v_n])){
                        // try to resolve 
                        $n = null;
                        if ($cinfo->prefix){
                            $n = $cinfo->prefix.$v_n;
                        }
                        if (!$n || !isset($cinfo->columnInfo[$n])){
                            throw new IGKException("column not found");
                        }
                        $v_n = $n;
                    }
                    $column->clLinkColumn = $v_n;
                } 
            }
        }
    }
    /**
     * resolving link resol
     * @param DiagramEntityColumnInfo $column_info 
     * @return bool 
     * @throws Exception 
     */
    public static function IsRequestLinkResolution(DiagramEntityColumnInfo $column_info){
        list($link_column, $link_type) = igk_extract ($column_info, 'clLinkColumn|clLinkType');
        return $link_column && $link_type;
    }
    /**
     * load diagram helper 
     * @param mixed $diagram 
     * @param mixed $tables 
     * @return void 
     * @throws IGKException 
     */
    public static function LoadDiagramSchema(DiagramEntityAssociation $diagram, $tables){
        foreach ($tables as $table => $def) {
            $g = $diagram->entity($table);
            if ($desc = igk_getv($def, "Description")) {
                $g->setDescription($desc);
            }
            if (is_array($columnInfo = igk_getv($def, "ColumnInfo"))) {
                $g->addProperties($columnInfo);
            }
        }
    }
    /**
     * create a diagram entity association from schema
     * @param XmlNode $schemaNode 
     * @param null|string $db_name 
     * @return DiagramEntityAssociation|void 
     */
    public static function CreateEntityDiagramDiagramFromSchema(XmlNode $schemaNode, ?string $db_name=null){
        $v_result = null;
        $mi = \IGK\System\Database\SchemaMigration::LoadSchema($schemaNode, $v_result); 
        if (!empty($v_result)){
            $diagram = new DiagramEntityAssociation();
            $diagram->db_name = $db_name;
            self::LoadDiagramSchema($diagram, $mi->table);
            return $diagram;
        }
    }
}
