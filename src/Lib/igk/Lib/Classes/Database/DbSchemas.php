<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbSchemas.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Database;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Controllers\SysDbControllerManager;
use IGK\Database\SchemaBuilder\DiagramEntityAssociation;
use IGK\Database\SchemaBuilder\SchemaDiagramVisitor;
use IGK\Helper\Database;
use IGK\System\Caches\DBCaches;
use IGK\System\Console\Logger;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\XML\XmlNode;
use IGKApp;
use IGKException;
use stdClass;
use function igk_resources_gets as __;


///<summary> schema constant </summary>
/**
 * 
 *  schema constant
 */
abstract class DbSchemas
{
    const ENTRIES_TAG = IGK_ENTRIES_TAGNAME;
    const DATA_DEFINITION = IGK_DATA_DEF_TAGNAME;
    const ROW_TAG = "Row";
    const ROWS_TAG = "Rows";
    const MIGRATION_TAG = "Migration";
    const MIGRATIONS_TAG = "Migrations";
    const RELATIONS_TAG = "Relations";
    const RELATION_TAG = "Relation";
    const COLUMN_TAG = IGK_COLUMN_TAGNAME;
    const GEN_COLUMN = IGK_GEN_COLUMS;


    /**
     * loaded schema
     * @var array
     */
    static $sm_schemas = [];

    public static function LoadRelations($schema, $data)
    {
        $n = $schema->add(self::RELATIONS_TAG);
        foreach ($data as $m) {
            igk_xml_obj_2_xml($n->add(self::RELATION_TAG), $m, true);
        }
        return $n;
    }
    public static function LoadMigrations($schema, $data)
    {
        $n = $schema->add(self::MIGRATIONS_TAG);
        foreach ($data as $m) {
            igk_xml_obj_2_xml($n->add(self::MIGRATION_TAG), $m, true);
        }
        return $n;
    }
    public static function LoadEntries($schema, $data)
    {
        $n = $schema->add(self::ENTRIES_TAG);
        foreach ($data as $m) {
            igk_xml_obj_2_xml($n->add(self::MIGRATION_TAG), $m, true);
        }
        return $n;
    }
    public static function __callStatic($name, $arguments)
    {
        die("call static method not allowed." . __CLASS__);
    }


    /**
     * load schema definition  - 
     * @param mixed $file 
     * @param mixed $ctrl 
     * @param bool $resolvname 
     * @return IDbSchemaDefinitionResult|object 
     * @throws IGKException 
     */
    public static function LoadSchema($file, ?BaseController $ctrl = null, $resolvname = true, $operation = DbSchemasConstants::Migrate)
    {
        if (!file_exists($file)) {
            return null;
        }
        // igk_log( load data schema )
        $data = null;
        if (isset(self::$sm_schemas[$file])) {
            $data = self::$sm_schemas[$file];
        }
        // if ($ctrl == SysDbController::ctrl()){
        //     igk_dev_wln(__FILE__.":".__LINE__,  "init ... sysdb");
        // }

        if (!$data) {
            $data = self::GetDefinition(HtmlReader::LoadFile($file), $ctrl, $resolvname, $operation);
            // + init Check and update data
            if ($cl = $ctrl::resolvClass($ctrl, \Database\InitDbSchemaBuilder::class)) {
                // resolv core entries 
                $b = new $cl();
                $tr = DiagramEntityAssociation::LoadFromXMLSchema($data);
                if ($operation == DbSchemasConstants::Downgrade) {
                    $b->downgrade($tr);
                } else {
                    $b->up($tr);
                }
                $tr->render(new SchemaDiagramVisitor($ctrl, $data));
            }
            self::$sm_schemas[$file] = ["controller" => $ctrl, "definition" => $data];
        } else {
            $data = $data["definition"];
        }
        return $data;
    }
    /**
     * get schema definition from node
     * @param XmlNode $d schema definition node
     * @param null|IGK\Controllers\BaseController $ctrl base controller 
     * @param bool $resolvname ressolv name
     * @return object 
     * @throws IGKException 
     */
    public static function GetDefinition(XmlNode $d, ?BaseController $ctrl = null, bool $resolvname = true, string $operation = DbSchemasConstants::Migrate)
    {
        $tables = array();
        $migrations = [];
        $relations = [];
        $output = null;
        if ($d) {
            $n = igk_getv($d->getElementsByTagName(IGK_SCHEMA_TAGNAME), 0);
            if ($n) {
                $output = self::LoadSchemaArray($n, $tables, $relations, $migrations, $ctrl, $resolvname, $operation);
            }
        }
        return (object)$output;
    }
    /**
     * loadd schema array
     * @param mixed $n 
     * @param mixed $tables 
     * @param mixed $tbrelations 
     * @param mixed $migrations 
     * @param mixed $ctrl 
     * @param bool $resolvname 
     * @param bool $reload 
     * @return mixed 
     */
    public static function LoadSchemaArray(
        $n,
        &$tables,
        &$tbrelations = null,
        &$migrations = null,
        $ctrl = null,
        $resolvname = true,
        $operation = DbSchemasConstants::Migrate,
        $reload = false
    ) {
        $key = "schema_load";
        if ($ctrl) {

            if (!$reload && IGKApp::IsInit() && ($tk = $ctrl->getEnvParam($key))) {
                extract($tk);
                return $tk;
            }
            $key = $ctrl->getEnvKey($key);
        }
        $v_result = null;
        $mi = \IGK\System\Database\SchemaMigration::LoadSchema(
            $n,
            $v_result,
            $tables,
            $tbrelations,
            $migrations,
            $ctrl,
            $resolvname,
            $reload,
            $operation
        );
        igk_environment()->set($key, $v_result);
        return $v_result;
    }
    /**
     * create and empty table row
     * @return stdClass|null 
     */
    public static function CreateRow(string $tablename, ?BaseController $ctrl = null, $dataobj = null): ?object
    { 
        $v_info = self::GetTableRowReference($tablename, $ctrl, $dataobj);
        if ($v_info) { 
            return self::CreateObjFromInfo($v_info, $dataobj);
        }
        return null;
    }    
    /**
     * table reference helper 
     * @param string $tablename 
     * @param null|BaseController $ctrl 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetTableRowReference(string $tablename, ?BaseController $ctrl = null)
    {
        return  DBCaches::GetInfo($tablename, $ctrl);
    }
    /**
     * create object from info Key refererence
     * @param array<IDbColumnInfo>|object<IDbColumnInfo> $tableRowReference 
     * @param mixed $dataobj object to reference of
     */
    public static function CreateObjFromInfo($tableRowReference, $dataobj = null): ?object
    {
        // + | --------------------------------------------------------------------
        // + | tableRowRefence [ column = > IDbColumInfo ]
        // + |        
        if (empty($tableRowReference))
            return null;
        $obj = igk_createobj();
        foreach ($tableRowReference as $k => $v) {
            if (!($v instanceof IDbColumnInfo)) {
                if (igk_environment()->isDev()) {
                    igk_dev_wln_e("failed : tableRowReference value is not a IDbColumnInfo", $tableRowReference);
                }
                continue;
            }
            $cl = DbColumnInfo::GetRowDefaultValue($v);
            $n = igk_getv($v, "clName", is_numeric($k) ? "column_" . $k : $k);
            $obj->$n = $cl;
        }
        if ($dataobj != null) {
            if (is_array($dataobj))
                $dataobj = (object)$dataobj;

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

    public static function InitData(BaseController $ctrl, $dataschema, $adapter)
    {
        $r = $dataschema;
        if (!is_object($r)) {
            throw new IGKException("dataschema not an object");
        }
        if ($ctrl == SysDbController::ctrl()) {
            Logger::info("init system database");
        }
        $tb = $r->Data;
        $etb = $r->Entries;
        $no_error = 1;
        if ($tb) {
            \IGK\Helper\Database::CreateTableBase($ctrl, $tb, $etb);
        }
        return $no_error;
    }

    /**
     * reset loading schema
     * @return void 
     */
    public static function ResetSchema()
    {
        self::$sm_schemas = [];
    }
}
