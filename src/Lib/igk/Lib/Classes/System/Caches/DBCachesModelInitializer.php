<?php
// @author: C.A.D. BONDJE DOUE
// @file: DBCachesModelInitializer.php
// @date: 20221120 23:53:26
namespace IGK\System\Caches;

use IGK\Controllers\BaseController;
use IGK\Controllers\ControllerExtension;
use IGK\Controllers\SysDbController;
use IGK\Controllers\SysDbControllerManager;
use IGK\Helper\Database;
use IGK\Helper\SysUtils;
use IGK\System\Console\Logger;
use IGK\System\Database\DbUtils;
use IGK\System\IO\File\PHPScriptBuilder;
use IGKSysUtil;

///<summary></summary>
/**
* 
* @package IGK\System\Caches
*/
class DBCachesModelInitializer{
    private $tableInfo;
    private $m_loaded = [];

    public static function Init($plist){
        $item = new self;
        $item->tableInfo = $plist;
        $item->m_loaded = [];
        $item->bootStrap();
        return $item;       
    }
    public function bootStrap(){
        $current = SysDbController::ctrl();
        $plist = (object)['tables'=>[], 'defs'=>[]];
        foreach($this->tableInfo as $ab){
            if ($ab->controller != $current){
                ControllerExtension::InitDataBaseModel(
                    $current,
                    $plist->tables);
                $this->_loadDef($current,  $plist->defs);
                $current = $ab->controller;
                $plist->tables = [];
                $plist->defs = [];
            }
            if (isset($plist->tables[$ab->tableName])){
                Logger::warn("possible re_use table ".$ab->tableName);
            }
            if (is_null($ab->definitionResolver)){
                $ab->definitionResolver = $this;
            }
            $plist->tables[$ab->tableName] = $ab;
            $plist->defs[$ab->tableName] = $ab;
        }
        ControllerExtension::InitDataBaseModel(
            $current,
            $plist->tables);
        $this->_loadDef($current,  $plist->defs);
        $plist->tables = [];
        $plist->defs = [];
    }

    private function _loadDef($current, $defs){
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
        $ns =  $ctrl::ns("");
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

            // + get property type
            $pr_type =   
                $this->getPhpDoPropertyType($cinfo->clName, $cinfo, $ctrl, false);
             
            $php_doc .= "@property " . $pr_type . "\n";
        }

        $args = $this->dBGetPhpDocModelArgEntries($columnInfo->columnInfo, $ctrl);

        if ($args) {
            $t_args = implode(", ", $args);
            $php_doc .= "@method static ?self Add(" . $t_args . ") add entry helper\n";
            $php_doc .= "@method static ?self AddIfNotExists(" . $t_args . ") add entry if not exists. check for unique column.\n";
        }

        if ($key != "clId") {
            if (is_array($key)) {
                $key = "['" . implode("','", array_keys($key)) . "']";
            } else {
                $key = "\"{$key}\"";
            }
            $o .= "/**\n*override primary key \n*/\n";
            $o .= "protected \$primaryKey = $key;" . PHP_EOL;
        }
        if (!empty($refkey) && ($refkey != "clId")) {
            $o .= "/**\n*override refid key \n*/\n";
            $o .= "protected \$refId = \"{$refkey}\"; " . PHP_EOL;
        }
        $base_ns = implode("\\", array_filter([$ns, "Models"]));
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
            $t = "int|" . $this->getLinkType($info->clLinkType, $info->clNotNull, $ctrl);
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
    public function getLinkType($type, bool $notnull, ?BaseController $ctrl = null)
    {
        $t = "";
        if (!$notnull) {
            $t .= "?";
        }
        $t .= "\\";
        $g = $this->tableInfo; // &\IGK\Models\ModelBase::RegisterModels();

        if (isset($g[$type])) {
            if (!isset($g[$type]->modelClass)) {
                igk_die(" model class not definited.");
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
                if ($gu = igk_getv($this->m_loaded[get_class($q)], $type)) {
                    break;
                }
            }
            if (is_null($gu)) {
                $gm = Database::GetInfo($type);
                igk_die(sprintf("try to retrieve null %s ", [$type]));
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