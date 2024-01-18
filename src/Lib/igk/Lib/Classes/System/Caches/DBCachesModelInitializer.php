<?php
// @author: C.A.D. BONDJE DOUE
// @file: DBCachesModelInitializer.php
// @date: 20221120 23:53:26
namespace IGK\System\Caches;

use IGK\Controllers\BaseController;
use IGK\Controllers\ControllerExtension;
use IGK\Controllers\SysDbController; 
use IGK\Database\DbColumnInfoPropertyConstants;
use IGK\Helper\Database;
use IGK\Helper\StringUtility;
use IGK\Models\ModelBase;
use IGK\System\Console\Logger;
use IGK\System\Database\DbUtils;
use IGK\System\IO\File\PHPScriptBuilder;
use IGKConstants;
use IGKException;
use IGKSysUtil;

///<summary></summary>
/**
* job is to initialize model data definition class
* @package IGK\System\Caches
*/
class DBCachesModelInitializer{
    private $tableInfo;
    private $m_loaded = [];
    /**
     * instance for migration 
     * @var bool
     */
    private $m_migration = false;

    private function __construct(){        
    }
    /**
     * create an instance for migration purpose
     * @return DBCachesModelInitializer 
     */
    public static function InitMigration($plist){
        $item = new self;
        $item->tableInfo = $plist;
        $item->m_loaded = []; 
        $item->m_migration = true;
        return $item;
    }
    /**
     * Init initializer with loaded 
     * @param array $plist array of definition table
     * @return DBCachesModelInitializer 
     * @throws IGKException 
     */
    public static function Init($plist, bool $force=false, bool $clean=false){
        $item = new self;
        $item->tableInfo = $plist;
        $item->m_loaded = [];
        $item->bootStrap($force, $clean);
        return $item;       
    }
    /**
     * boot and init model base
     * @param bool $force 
     * @return void 
     * @throws IGKException 
     */
    public function bootStrap(bool $force=false, bool $clean=false){
        if (!$this->tableInfo){
            return;
        }
        $current = null;//  SysDbController::ctrl();
        $plist = (object)['tables'=>[], 'defs'=>[]];
        foreach($this->tableInfo as $ab){
            if (is_null($current)){
                $current = $ab->controller ?? igk_die('no provided controller');
            }
            if ($ab->controller != $current){
                $this->_loadDef($current,  $plist->defs);
                ControllerExtension::InitDataBaseModel(
                    $current,
                    $plist->tables, $force);
                $current = $ab->controller;
                $plist->tables = [];
                $plist->defs = [];
            }
            if (isset($plist->tables[$ab->tableName])){
                Logger::warn("possible re_use table ".$ab->tableName);
            }
  

            if (!isset($ab->definitionResolver) || is_null($ab->definitionResolver)){
                $ab->definitionResolver = $this;
            }
            if (!isset($ab->modelClass) || is_null($ab->modelClass )){
                $table = igk_getv($ab, DbColumnInfoPropertyConstants::DefTableName);
                $table = basename(igk_uri(IGKSysUtil::GetModelTypeName($table), $current));
                $ns = $current::ns('');
                // $current->resolveClass('Models/'.$table);
                $ab->modelClass = $ns."\\Models\\".$table;
            }

            $plist->tables[$ab->tableName] = $ab;
            $plist->defs[$ab->tableName] = $ab;
        }
        if ($current ){
            if (!($current instanceof BaseController)){
                echo '<pre>';
                print_r($plist);
                echo '</pre>';
                exit;
            }
            
            $this->_loadDef($current,  $plist->defs);
            ControllerExtension::InitDataBaseModel(
                $current,
                $plist->tables, $force, $clean);
        }
        $plist->tables = [];
        $plist->defs = [];
    }
    /**
     * 
     * @param BaseController $current 
     * @param mixed $defs 
     * @return void 
     */
    private function _loadDef(BaseController $current, $defs){
        $cl = get_class($current);
        if (!isset($this->m_loaded[$cl])){
            $this->m_loaded[$cl] = [];
        }
        $this->m_loaded[$cl] = array_merge($this->m_loaded[$cl], $defs);
    }
     /**
     * get model source declaration
     * @param mixed $name 
     * @param mixed $table 
     * @param mixed $columnInfo 
     * @param mixed $ctrl 
     * @param null|string $comment 
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function getModelDefaultSourceDeclaration($name, $table, $columnInfo, $ctrl, ?string $comment = null)
    {
        $ns =  $ctrl->getEntryNamespace(); 
        $uses = [];
        $gc = 0;
        $extends = implode("\\", array_filter([$ns, "Models\\ModelBase"]));
        $c = $ctrl->getClassesDir() . "/Models/";
        if (($name != "ModelBase") && file_exists($c . "/ModelBase.php")) {
            $uses[] =  implode("\\", array_filter([$ns, "Models\\ModelBase"]));
            $gc = 1;
        } else {
            $uses[] = ModelBase::class;
        }
        $o = "/**\n* table's name\n*/\n";
        $o .= "protected \$table = \"{$table}\"; " . PHP_EOL;

        if (!$gc && $ctrl) {
            $cl = get_class($ctrl);
            $uses[] = "$cl::class";
            $o .= "\t/**\n\t */\n";
            $o .= "\tprotected \$controller = {$cl}::class; " . PHP_EOL;
        }
        if (($columnInfo instanceof  \IGK\Database\DbColumnInfo)) {
            $columnInfo = (object)["columnInfo" => [$columnInfo]];
        }
        $key = "";
        $refkey = "";
        $php_doc = "";
        $const_data = "";
        $hiddens = [];
        $unique_columns = [];
        $displays = [];
        foreach ($columnInfo->columnInfo as $cinfo) {
            if ($cinfo->clIsPrimary) {
                if (!empty($key)) {
                    if (!is_array($key)) {
                        $key = [$key => $key];
                    }
                    $key[$cinfo->clName] =  $cinfo->clName;
                } else {
                    $key = $cinfo->clName;
                }
            }
            if ($cinfo->getIsRefId()) {
                $refkey = $cinfo->clName;
            }
            if ($cinfo->clHide){
                $hiddens[] = $cinfo->clName;
            }
            if ($cinfo->clIsUniqueColumnMember){
                if (!($index = $cinfo->clColumnMemberIndex)){
                    $index = 0;
                }
                $unique_columns[$index][] = $cinfo->clName;
            }

            if ($cinfo->clDisplay){
                $displays[] = $cinfo->clName;
            }

            // + get property type
            $pr_type =   
                $this->getPhpDoPropertyType($cinfo->clName, $cinfo, $ctrl, false);
             if ($desc = trim($cinfo->clDescription ??'')){
                $desc = ' '.$desc;
             }
            $php_doc .= sprintf("@property " . $pr_type . "%s\n", $desc);
            $const_data .=  "const FD_".StringUtility::GetConstantName($cinfo->clName). '="'.$cinfo->clName.'";' ."\n";

        }

        $args = $this->dBGetPhpDocModelArgEntries($columnInfo->columnInfo, $ctrl);

        if ($args) {
            $t_args = implode(", ", $args);
            $php_doc .= "@method static ?self Add(" . $t_args . ") add entry helper\n";
            $php_doc .= "@method static ?self AddIfNotExists(" . $t_args . ") add entry if not exists. check for unique column.\n";
        }
        if ($macros_cl = $ctrl->resolveClass(IGKConstants::NS_MACROS_CLASS.'\\'.$name.'Macros')){
            
            $m = Database::GetPhpDocMacrosDefintionToInjectFromMacroClass($macros_cl);
            $php_doc.= $m;
        }


        if ($key != "clId") {
            if (is_array($key)) {
                $key = "['" . implode("','", array_keys($key)) . "']";
            } else {
                $key = "\"{$key}\"";
            }
            $o .= "/**\n* override primary key \n*/\n";
            $o .= "protected \$primaryKey = $key;" . PHP_EOL;
        }
        if (!empty($refkey) && ($refkey != "clId")) {
            $o .= "/**\n* override refid key \n*/\n";
            $o .= "protected \$refId = \"{$refkey}\"; " . PHP_EOL;
        }

        if (!empty($hiddens)) {
            $o .= "/**\n*override hidden key \n*/\n";
            $_hidden = "['".implode("','", $hiddens)."']";
            $o .= "protected \$hidden = {$_hidden}; " . PHP_EOL;
        }

        if (!empty($displays)) {
            $o .= "/**\n*override hidden key \n*/\n";
            $_display = count($displays)==1 ? "'".$displays[0]."'" :  "['".implode("','", $displays)."']";
            $o .= "protected \$display = {$_display}; " . PHP_EOL;
        }

        if (!empty($unique_columns)){

            $o .= "protected \$unique_columns = ".var_export($unique_columns, true).";";
        }

        $base_ns = implode("\\", array_filter([$ns, "Models"]));
        $o = $const_data . $o;
        $builder = new PHPScriptBuilder();
        $builder->type("class")
            ->author(IGK_AUTHOR)
            ->extends($extends)
            ->name($name)
            ->namespace($base_ns)
            ->defs($o)
            ->file($name . ".php")
            ->doc($comment)
            ->phpdoc(rtrim($php_doc) . "\n")
            ->uses($uses);
        if (empty($columnInfo->modelClass)){
            $columnInfo->modelClass =    $base_ns . "\\".$name;
        }
        $cf = $builder->render();
        return $cf;
    }

     /**
     * 
     * @param array $inf 
     * @param BaseController $ctrl 
     * @return array 
     */
    public function dBGetPhpDocModelArgEntries(array $inf, BaseController $ctrl)
    {
        $tab = [];
        $require = [];
        $optional = [];
        $skeys = [];
        foreach ($inf as $column => $prop) {
            if (is_integer($column))
            {
                $column = $prop->clName;
            }
            $skeys[$column] = $prop;

            if ($prop->clAutoIncrement) {
                continue;
            }
            if ($prop->clDefault) {
                $optional[] = $column;
                continue;
            }
            $require[] = $column;
            
        }
        $tab = array_merge($require, $optional);
        $tab = array_combine($tab, $tab); 

        $g = array_map(function ($i) use ($ctrl, $skeys) {
            return $this->getPhpDoPropertyType($i, $skeys[$i], $ctrl, true);
        }, $tab);
        return $g;
    }

    /**
     * 
     * @param string $type 
     * @return void 
     */
    public function getPhpDocDefaultLinkType(string $type){
        $type =strtolower($type);
        switch ($type) {
            case 'int':
            case 'uint':
            case 'bigint':
            case 'ubigint':
                return 'int';
            default:
                # code...
                break;
        }
        return 'string';
    }
     /**
     * 
     * @param mixed $name 
     * @param mixed $info 
     * @param BaseController $ctrl 
     * @param bool $extra 
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function getPhpDoPropertyType($name, $info, BaseController $ctrl, $extra = false)
    { 
        $t = IGKSysUtil::ConvertToPhpDocType($info->clType);
        
        if ($info->clLinkType) {
            $default_link = 'int';
            // + | --------------------------------------------------------------------
            // + | because getLinkType resolve the table cache list it must be call first 
            // + |
            
            $tp = $this->getLinkType($info->clLinkType, $info->clNotNull, $ctrl);
            if ($info->clLinkColumn){
                $tb = $info->clLinkType; // DbUtils::ResolvDefTableTypeName($info->clLinkType, $ctrl);
                if (!isset($this->tableInfo[$tb])){
                    igk_die("failed to resolv the table info ".$tb);
                }
                $clinf = igk_getv($this->tableInfo[$tb]->columnInfo, $info->clLinkColumn);  
                if ($clinf){              
                    $default_link = $this->getPhpDocDefaultLinkType($clinf->clType);
                }else{
                    igk_die(sprintf("reflink column not found .%s.%s ", $tb, $info->clLinkColumn));
                }
            } 
            $t = implode('|', array_filter([$default_link, 
            $tp ]));
        }
        $extra = "";
        if ($info->clDefault) {
            $extra .= " =\"" . $info->clDefault . "\"";
        }
        // + | --------------------------------------------------------------------
        // + | comment 
        // + | 
        return $t . " \$" . $name . $extra;
    }

     /**
     * Get Link type helper
     * @param mixed $type 
     * @param bool $notnull 
     * @param null|BaseController $ctrl 
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function getLinkType($type, ?bool $notnull, ?BaseController $ctrl = null)
    {
        $gu = null;
        $t = "";
        if (!$notnull) {
            $t .= "?";
        }
        $t .= "\\";
        $g = $this->tableInfo;  
        if (isset($g[$type])) {
            if (!isset($g[$type]->modelClass)) {
                igk_die("$type model class not defined.");
            }
            $t .= $g[$type]->modelClass;
        } else {
            // retrieve model 
            $list = [];
            if ($ctrl){
                $list[] = $ctrl;
                $type = igk_db_get_table_name($type, $ctrl);
            }
            if (SysDbController::ctrl() !== $ctrl) {
                $list[] = SysDbController::ctrl();
            }
            while ($q = array_shift($list)) {
                if (isset($g[$type])){
                    $gu = $g[$type];
                    break;
                }

                if (isset($this->m_loaded[get_class($q)]) && ($gu = igk_getv($this->m_loaded[get_class($q)], $type)) ){
                    break;
                }
                if (!isset($this->m_loaded[get_class($q)]) && $this->m_migration){
                    // + | need to load schema of the controller data 
                    // DBCaches::GetTableInfo($type,)
                    DBCaches::Init();
                    $this->m_migration = false; 
                }
            }
            if (is_null($gu)) {
                $info = DBCaches::GetTableInfo($type);
                if (!is_null($info)){
                    $gu = $info;
                    $ctrl = $gu->controller;
                    $this->tableInfo[$type] = $info;
                } else{
                // $gm = Database::GetInfo($type);
                    igk_die(sprintf("try to retrieve AT : ".__CLASS__." no info table for [%s] ", $type));
                    return null;
                }
            }
            if (!isset($gu->modelClass)) {
                if (!isset($gu->defTableName)){
                    $gu->defTableName = DbUtils::ResolvDefTableTypeName($type, $ctrl);  
                }
                $gu->modelClass = IGKSysUtil::GetModelTypeName($gu->defTableName, $ctrl);
            }
            $t .=  $gu->modelClass;
        }
        return $t;
    }
}