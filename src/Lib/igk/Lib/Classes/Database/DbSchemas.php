<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbSchemas.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Database;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Controllers\SysDbControllerManager;
use IGK\System\Html\HtmlReader;
use IGKException;
use stdClass;
use function igk_resources_gets as __ ;


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
    const COLUMN_TAG = IGK_COLUMN_TAGNAME;


    /**
     * loaded schema
     * @var array
     */
    static $sm_schemas = [];

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
        die("call static method not allowed.".__CLASS__);
    }

   

    public static function LoadSchema($file, $ctrl = null, $resolvname = true){
        
        if (!file_exists($file)) {
            return null;
        }
        $data = igk_getv(self::$sm_schemas, $file);
        if (!$data){
            $data = self::GetDefinition(HtmlReader::LoadFile($file), $ctrl, $resolvname);
            self::$sm_schemas[$file] = ["controller"=>$ctrl, "definition"=>$data];
        }else {            
            $data = $data["definition"];
        }
        return $data;
    }
    /**
     * get schema definition from node
     * @param HtmlNode $d schema definition node
     * @param null|IGK\Controllers\BaseController $ctrl base controller 
     * @param bool $resolvname ressolv name
     * @return object 
     * @throws IGKException 
     */
    public static function GetDefinition($d, ?BaseController $ctrl=null, bool $resolvname=true){
        $tables = array();
        $migrations = [];
        $relations = [];
        $output = null;
        if ($d) {
            $n = igk_getv($d->getElementsByTagName(IGK_SCHEMA_TAGNAME), 0);
            if ($n) {
                $output = igk_db_load_data_schema_array($n, $tables, $relations, $migrations, $ctrl, $resolvname);
            }
        }
        
        return (object)$output;
    }
    /**
     * create and empty table row
     * @return stdClass|null 
     */
    public static function CreateRow(string $tablename, ?BaseController $ctrl=null, $dataobj = null){ 
        $inf = self::GetTableRowReference($tablename, $ctrl);
        if ($inf){
            return self::CreateObjFromInfo($inf, $dataobj);
        }
    }
    public static function GetTableRowReference(string $tablename, ?BaseController $ctrl=null){
        $g = SysDbControllerManager::GetDataTableDefinitionFormController($ctrl, $tablename);
        if ($g){ 
            return igk_getv($g, "tableRowReference"); 
        }       
    }
    /**
     * create object from info Key refererence
     */
    public static function CreateObjFromInfo($tableRowReference, $dataobj=null){
        if ($tableRowReference) {
            $obj = igk_createobj();
            foreach ($tableRowReference as $k => $v) {
                $cl = DbColumnInfo::GetRowDefaultValue($v);  
                $n = igk_getv($v, "clName", is_numeric($k) ? "column_".$k : $k);
                $obj->$n = $cl;
            }
            if ($dataobj != null) {
                if (is_array($dataobj))
                    $dataobj = (object)$dataobj;
                // igk_db_copy_row($obj, $dataobj);

                foreach ($obj as $k => $v) {
             
                    if (isset($dataobj->$k)) {
                        $obj->$k = $dataobj->$k;
                    } else { 
                        $obj->$k = null;
                    }
                }
            }
            return $obj;
        }
        return null;
    }

    public static function InitData(BaseController $ctrl, $dataschema, $adapter){
        $r = $dataschema;
        if (!is_object($r)) {
            throw new IGKException("dataschema not an object");
        }
        $tb = $r->Data;
        $etb = $r->Entries;
        $no_error = 1;
        if ($tb){
            $adapter->beginInitDb($ctrl);
            foreach ($tb as $k => $v) {
                $n = igk_db_get_table_name($k, $ctrl);
                $data = igk_getv($etb, $n);
                igk_hook(IGK_NOTIFICATION_INITTABLE, [$ctrl, $n, &$data]);
            
                if (!$adapter->createTable($n, igk_getv($v, 'ColumnInfo'), $data, igk_getv($v, 'Description'), $adapter->DbName)) {
                    igk_push_env("db_init_schema", __("failed to create [0]", $n));
                    $no_error = 0;
                }
            }
            $adapter->endInitDb();
        } 
        return $no_error;
    }
   
}