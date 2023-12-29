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
use IGK\System\Database\SchemaMigrationInfo;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\XML\XmlNode;
use IGKApp;
use IGKEvents;
use IGKException;
use LlvGStockController;
use ReflectionException;
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
    const GEN_COLUMNS = IGK_GEN_COLUMS;
    const RT_REQUIRESCHEMA_TAG = "RequireSchema";
    const RT_SCHEMA_TAG = IGK_SCHEMA_TAGNAME;

    /**
     * 
     * @var mixed
     */
    private static $sm_isLoadingFromSchema;

    /**
     * loaded schema
     * @var array
     */
    static $sm_schemas = [];

    /**
     * is loading from schema
     * @return ?bool 
     */
    public static function IsLoadingFromSchema():?bool{
        return self::$sm_isLoadingFromSchema;
    }
    /**
     * clear store controller schema
     * @param BaseController $controller 
     * @return void 
     */
    public static function ClearControllerSchema(BaseController $controller){
        $v_tab = & self::$sm_schemas;
        if ($file = $controller->getDataSchemaFile()){
            unset($v_tab[$file]);
        }
        
    }

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

    public static function schemaDef(){
        $file=  '/Volumes/Data/Dev/PHP/balafon2/src/Lib/igk/Data/data.schema.xml';
        if (isset(self::$sm_schemas[$file])){
            return self::$sm_schemas[$file];
        }
        return null;
    }

    /**
     *  
     * load schema definition  - 
     * @param string $file 
     * @param mixed $ctrl   
     * @param bool $resolvname  resolv name on loading
     * @param string $operation in migration operation
     * @return ?\IGK\System\Database\ILoadSchemaInfo 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException  
     */
    public static function LoadSchema(string $file, ?BaseController $ctrl = null, $resolvname = true, $operation = DbSchemasConstants::Migrate)
    {
        if (!file_exists($file)) {
            return null;
        } 
        $data = null;
        if (isset(self::$sm_schemas[$file])) {
            $data = self::$sm_schemas[$file];
        }  
        if (!$data) {    
            $xcode = HtmlReader::LoadFile($file);
            if (!$xcode){
                return null;
            }
            
            self::$sm_isLoadingFromSchema = 1;
            $data = self::GetDefinition($xcode, $ctrl, $resolvname, $operation);      
            // + init Check and update data
            if ($ctrl && ($cl = $ctrl->resolveClass(\Database\InitDbSchemaBuilder::class))) {
                // resolv core entries 
                $b = new $cl();
                $tr = DiagramEntityAssociation::LoadFromXMLSchema($data);
                if ($operation == DbSchemasConstants::Downgrade) {
                    $b->downgrade($tr);
                } else { 
                    $b->upgrade($tr);
                }
                // change the data definition - after Operation. 
                $tr->render(new SchemaDiagramVisitor($ctrl, $data, $operation));
            }
            self::$sm_schemas[$file] = ["controller" => $ctrl, "definition" => $data];
            self::$sm_isLoadingFromSchema = 0;
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
        $entries = [];
        $output = null;
        if ($d) {
            $n = igk_getv($d->getElementsByTagName(IGK_SCHEMA_TAGNAME), 0);
            if ($n) {
                $output = self::LoadSchemaArray($n, $tables, $relations, $migrations, $entries, $ctrl, $resolvname, $operation);
            }
        }
        return (object)$output;
    }
    /**
     * loadd schema array
     * @param mixed $n 
     * @param array $tables 
     * @param array $tbrelations 
     * @param array $migrations 
     * @param array $entries 
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
        &$entries = null,
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
        \IGK\System\Database\SchemaMigration::LoadSchema(
            $n,
            $v_result,
            $tables,
            $tbrelations,
            $migrations,
            $entries,
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
        static $sm_cacheinfo = null; 
        if (is_null($sm_cacheinfo)){
            $sm_cacheinfo = [];
        }
        $key = $ctrl ? $ctrl->getEnvKey('db-cache/'.$tablename) : $tablename;
        if (isset($sm_cacheinfo[$key])){
            $v_info = $sm_cacheinfo[$key];
            return self::CreateObjFromInfo($v_info, $dataobj);
        } 
 
        $v_info = DBCaches::GetColumnInfo($tablename, $ctrl) ?? self::GetTableRowReference($tablename, $ctrl, $dataobj);

        if ($v_info) { 
            if ($v_info instanceof SchemaMigrationInfo){
                $v_info = $v_info->columnInfo;
            }
            $sm_cacheinfo[$key] = $v_info;
            return self::CreateObjFromInfo($v_info, $dataobj);
        }
        return null;
    }  
    /**
     * get table info
     * @param string $tablename 
     * @param null|BaseController $ctrl 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetTableColumnInfo(string $tablename, ?BaseController $ctrl=null){
        $v_info = DBCaches::GetColumnInfo($tablename, $ctrl) ?? self::GetTableRowReference($tablename, $ctrl, null);
        if ($v_info) { 
            if ($v_info instanceof SchemaMigrationInfo){
                $v_info = $v_info->columnInfo;
            }
            return $v_info;
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
        return  DBCaches::GetColumnInfo($tablename, $ctrl);
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
                    igk_dev_wln_e("failed : [$k] tableRowReference value is not a IDbColumnInfo"); 
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

    /**
     * init data schemas
     * @param BaseController $ctrl
     * @param object|ISchemaInfo $dataschema schema info, 
     * @param object $adapter data adapter 
     */
    public static function InitData(BaseController $ctrl, $dataschema, $adapter)
    {
        $r = $dataschema;
        if (!is_object($r)) {
            throw new IGKException("dataschema not an object");
        }
        if ($ctrl == SysDbController::ctrl()) {
            Logger::info("init system database ...");
        }
        $tb = $r->Data;
        $etb = $r->Entries;
        $no_error = 1;
        if ($tb) { 
            \IGK\Helper\Database::CreateTableBase($ctrl, $tb, $etb, $adapter);
        }
        // UPDATE REQUIRED MIGRATION
        try{ 
            igk_hook(IGKEvents::HOOK_DB_MIGRATE, ['ctrl'=>$ctrl,'type'=>'init', 'data'=>$r]);
        } catch(\Exception $ex){
            Logger::danger(implode("\n", [__METHOD__, $ex->getMessage()]));
            $no_error = 1;
        }
        
        return $no_error;
    }

    /**
     * reset loading schema - 
     * @return void 
     */
    public static function ResetSchema()
    {
        self::$sm_schemas = [];
    }
}
