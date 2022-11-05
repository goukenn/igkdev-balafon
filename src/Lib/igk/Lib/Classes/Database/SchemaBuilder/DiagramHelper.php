<?php


// @author: C.A.D. BONDJE DOUE
// @filename: DiagramHelper.php
// @date: 20220728 12:16:48
// @desc: diagram helper 

namespace IGK\Database\SchemaBuilder;

use IGK\System\Html\XML\XmlNode;

abstract class DiagramHelper{

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
