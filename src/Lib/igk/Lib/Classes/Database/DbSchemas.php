<?php
namespace IGK\Database;

///<summary> schema constant </summary>
/**
 * 
 *  schema constant
 */
abstract class DbSchemas{    
    const ENTRIES_TAG = IGK_ENTRIES_TAGNAME;  
    const DATA_DEFINITION = IGK_DATA_DEF_TAGNAME;
    const ROW_TAG = "Row";
    const ROWS_TAG = "Rows";
    const MIGRATION_TAG = "Migration";
    const MIGRATIONS_TAG = "Migrations";
    const RELATIONS_TAG = "Relations";
    const RELATION_TAG = "Relation";


    public static function LoadRelations($schema, $data){
        $n = $schema->add(self::RELATIONS_TAG);
        foreach($data as $m){          
            igk_xml_obj_2_xml($n->add(self::RELATION_TAG), $m, true);
        }
        return $n;
    }
    public static function LoadMigrations($schema, $data){
        $n = $schema->add(self::MIGRATIONS_TAG);
        foreach($data as $m){          
            igk_xml_obj_2_xml($n->add(self::MIGRATION_TAG), $m, true);
        }
        return $n;
    }
    public static function LoadEntries($schema, $data){
        $n = $schema->add(self::ENTRIES_TAG);
        foreach($data as $m){          
            igk_xml_obj_2_xml($n->add(self::MIGRATION_TAG), $m, true);
        }
        return $n;
    }
    public static function __callStatic($name, $arguments)
    {
        die("not allowed");
    }
}