<?php
// @author: C.A.D. BONDJE DOUE
// @file: DBCachesModelInitializer.php
// @date: 20221120 23:53:26
namespace IGK\System\Caches;

use IGK\Controllers\BaseController;
use IGK\Controllers\ControllerExtension;
use IGK\Controllers\SysDbController;
use IGK\Database\DbColumnInfoPropertyConstants;
use IGK\Database\Models\Helper\InitClassBuilder;
use IGK\Helper\Database;
use IGK\Helper\StringUtility;
use IGK\Models\ModelBase;
use IGK\System\Console\Logger;
use IGK\System\Database\DbUtils;
use IGK\System\Database\Helper\DbUtility;
use IGK\System\Database\JoinTableOp;
use IGK\System\IO\File\PHPScriptBuilder;
use IGKConstants;
use IGKException;
use IGKSysUtil;

///<summary></summary>
/**
 * job is to initialize model data definition class
 * @package IGK\System\Caches
 */
class DBCachesModelInitializer
{
    private $tableInfo;
    private $m_loaded = [];
    /**
     * instance for migration 
     * @var bool
     */
    private $m_migration = false;

    private function __construct() {}
    /**
     * create an instance for migration purpose
     * @return DBCachesModelInitializer 
     */
    public static function InitMigration($plist)
    {
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
    public static function Init($plist, bool $force = false, bool $clean = false)
    {
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
    public function bootStrap(bool $force = false, bool $clean = false)
    {
        if (!$this->tableInfo) {
            return;
        }
        $current = null; //  SysDbController::ctrl();
        $plist = (object)['tables' => [], 'defs' => []];
        foreach ($this->tableInfo as $ab) {
            if (is_null($current)) {
                $current = $ab->controller ?? igk_die('no provided controller');
            }
            if ($ab->controller != $current) {
                $this->_loadDef($current,  $plist->defs);
                ControllerExtension::InitDataBaseModel(
                    $current,
                    $plist->tables,
                    $force
                );
                $current = $ab->controller;
                $plist->tables = [];
                $plist->defs = [];
            }
            if (isset($plist->tables[$ab->tableName])) {
                Logger::warn("possible re_use table " . $ab->tableName);
            }


            if (!isset($ab->definitionResolver) || is_null($ab->definitionResolver)) {
                $ab->definitionResolver = $this;
            }
            if (!isset($ab->modelClass) || is_null($ab->modelClass)) {
                $table = igk_getv($ab, DbColumnInfoPropertyConstants::DefTableName);
                $table = basename(igk_uri(IGKSysUtil::GetModelTypeName($table), $current));
                $ns = $current::ns(''); 
                $ab->modelClass = $ns . "\\Models\\" . $table;
            }

            $plist->tables[$ab->tableName] = $ab;
            $plist->defs[$ab->tableName] = $ab;
        }
        if ($current) {
            if (!($current instanceof BaseController)) {
                echo '<pre>';
                print_r($plist);
                echo '</pre>';
                igk_exit();
            }

            $this->_loadDef($current,  $plist->defs);
            ControllerExtension::InitDataBaseModel(
                $current,
                $plist->tables,
                $force,
                $clean
            );
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
    private function _loadDef(BaseController $current, $defs)
    {
        $cl = get_class($current);
        if (!isset($this->m_loaded[$cl])) {
            $this->m_loaded[$cl] = [];
        }
        $this->m_loaded[$cl] = array_merge($this->m_loaded[$cl], $defs);
    }
    /**
     * get model source declaration
     * @param mixed $name 
     * @param mixed $table 
     * @param mixed $migrationInfo 
     * @param mixed $ctrl 
     * @param null|string $comment 
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function getModelDefaultSourceDeclaration(string $name, string $table, $migrationInfo, BaseController $ctrl, 
    ?string $comment = null, ?string $prefix=null, ?string $display_expression=null)
    {
        $sb = InitClassBuilder::BuildInitialModelClass($name, $table, $migrationInfo, $ctrl, $comment, 
        $prefix,
        $display_expression,
        function($cinfo, $ctrl, $prefix){
            return $this->getPhpDoPropertyType($cinfo->clName, $cinfo, $ctrl, false, $prefix);
        },
        function ($migrationInfo, $ctrl, $prefix){
            return $this->dBGetPhpDocModelArgEntries($migrationInfo->columnInfo, $ctrl, $prefix);
        });
        return $sb;
    }

    /**
     * 
     * @param array $inf 
     * @param BaseController $ctrl 
     * @return array 
     */
    public function dBGetPhpDocModelArgEntries(array $inf, BaseController $ctrl, ?string $prefix)
    {
        $tab = [];
        $require = [];
        $optional = [];
        $skeys = [];
        foreach ($inf as $column => $prop) {
            if (is_integer($column)) {
                $column = $prop->clName;
            }
            if ($prefix){
                $column = self::_RemovePrefix($column, $prefix);
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
    public function getPhpDocDefaultLinkType(string $type)
    {
        $type = strtolower($type);
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
    protected static function _RemovePrefix(string $name, string $prefix){
        $ln = strlen($prefix);
        if (igk_str_startwith($name, $prefix) && ($prefix!=$name)){
            $name = substr($name, $ln);
        }
        return $name;
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
    public function getPhpDoPropertyType(string $name, $info, BaseController $ctrl, $extra = false, ?string $prefix= null)
    {
        if ($prefix){
            $name = self::_RemovePrefix($name, $prefix);
        }
        $t = IGKSysUtil::ConvertToPhpDocType($info->clType);

        if ($info->clLinkType) {
            $default_link = 'int';
            // + | --------------------------------------------------------------------
            // + | because getLinkType resolve the table cache list it must be call first 
            // + |

            $tp = $this->getLinkType($info->clLinkType, $info->clNotNull, $ctrl);
            if ($v_lnk_column = $info->clLinkColumn) {

                $tb = $info->clLinkType; // DbUtils::ResolvDefTableTypeName($info->clLinkType, $ctrl);

                if (!isset($this->tableInfo[$tb])) {
                    igk_die("failed to resolv the table info " . $tb);
                }
                $v_binfo = $this->tableInfo[$tb];
                $link_column = DbUtility::GetLinkColumn($v_binfo->columnInfo, $v_lnk_column, $v_binfo->prefix );
                $clinf = igk_getv($v_binfo->columnInfo,  $link_column );
                if ($clinf) {
                    $default_link = $this->getPhpDocDefaultLinkType($clinf->clType);
                } else {
                    igk_die(sprintf("reflink column not found [%s.%s]", $tb, $info->clLinkColumn));
                }
            }
            $t = implode('|', array_filter([
                $default_link,
                $tp
            ]));
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
            if ($ctrl) {
                $list[] = $ctrl;
                $type = igk_db_get_table_name($type, $ctrl);
            }
            if (SysDbController::ctrl() !== $ctrl) {
                $list[] = SysDbController::ctrl();
            }
            while ($q = array_shift($list)) {
                if (isset($g[$type])) {
                    $gu = $g[$type];
                    break;
                }

                if (isset($this->m_loaded[get_class($q)]) && ($gu = igk_getv($this->m_loaded[get_class($q)], $type))) {
                    break;
                }
                if (!isset($this->m_loaded[get_class($q)]) && $this->m_migration) {
                    // + | need to load schema of the controller data 
                    DBCaches::Init();
                    $this->m_migration = false;
                }
            }
            if (is_null($gu)) {
                $info = DBCaches::GetTableInfo($type);
                if (!is_null($info)) {
                    $gu = $info;
                    $ctrl = $gu->controller;
                    $this->tableInfo[$type] = $info;
                } else {
                    // $gm = Database::GetInfo($type);
                    igk_die(sprintf("try to retrieve AT : " . __CLASS__ . " no info table for [%s] ", $type));
                    return null;
                }
            }
            if (!isset($gu->modelClass)) {
                if (!isset($gu->defTableName)) {
                    $gu->defTableName = DbUtils::ResolvDefTableTypeName($type, $ctrl);
                }
                $gu->modelClass = IGKSysUtil::GetModelTypeName($gu->defTableName, $ctrl);
            }
            $t .=  $gu->modelClass;
        }
        return $t;
    }
}
