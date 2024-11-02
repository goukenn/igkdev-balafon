<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbUtility.php
// @date: 20230118 11:28:43
namespace IGK\System\Database\Helper;

use IGK\Controllers\BaseController;
use IGK\Database\DbExpression;
use IGK\Database\DbSchemas;
use IGK\Database\IDbColumnInfo;
use IGK\Models\ModelBase;
use IGK\System\Caches\DBCaches;
use IGK\System\Database\DbConditionExpressionBuilder;
use IGK\System\Database\DbReverseMappingLink;
use IGK\System\Html\Dom\HtmlNode;
use IGKException;
use IGKSysUtil;

///<summary></summary>
/**
 * database helper utility class 
 * @package IGK\System\Database\Helper
 */
abstract class DbUtility
{

    /**
     * prefix the column name with data value
     * @param string $columnName 
     * @param string $prefix 
     * @return string 
     */
    public static function TreatColumnName(string $columnName, ?string $prefix)
    {
        if ($prefix && !igk_str_startwith($columnName, $prefix)) {
            $columnName = $prefix . $columnName;
        }
        return $columnName;
    }

    /**
     * remove column prefix key 
     * @param string $columnName 
     * @param null|string $prefix 
     * @return string|string[]|null 
     */
    public static function RemoveColumnPrefixName(string $columnName, ?string $prefix){
        if ($prefix) {
            $columnName = preg_replace("/^" . $prefix . "/i",  "", $columnName);
        }
        return $columnName;
    }
    /**
     * 
     * @param BaseController $ctrl 
     * @param mixed $tables 
     * @return mixed 
     * @throws IGKException 
     */
    public static function ExportToXMLSchemaData(BaseController $ctrl, $tables)
    {
        $xml = HtmlNode::CreateWebNode('dbdataschema');
        $bprefix = igk_configs()->db_prefix;
        $prefix = IGKSysUtil::DBReverseTableName($bprefix, $ctrl);
        foreach ($tables as $t => $v) {
            $rep = $xml->addNode(DbSchemas::DATA_DEFINITION)->setAttributes(array(
                "TableName" => $v->defTableName,
                "RefKey" => null
            ));
            foreach ($v->columnInfo as $info) {
                $tab = $info->to_array();
                if ($lnk = $info->clLinkType) {
                    if ($p = igk_getv($tables, $lnk)) {
                        $tab['clLinkType'] = $p->defTableName;
                    } else {
                        if ($bprefix && (strpos($lnk, $bprefix) == 0)) {
                            $tab['clLinkType'] = $prefix . substr($lnk, strlen($bprefix));
                        }
                    }
                }
                $rep->add(DbSchemas::COLUMN_TAG)->setAttributes(array_filter($tab));
            }
            // if ($defentries)
            //     $appc->datadb("get_table_definition", $rep, $v, $apt, null, $entries);
        }
        return $xml;
    }
    /**
     * 
     * @param BaseController $ctrl 
     * @param mixed $options 
     * @return void 
     * @throws IGKException 
     */
    public static function UpdateDbSchema(BaseController $ctrl, $options = null)
    {
        $file = $ctrl::getDataSchemaFile();
        $schema = igk_db_load_data_schemas($file, $ctrl, true);
        $tables = igk_getv($schema, "tables");
        $n = self::ExportToXMLSchemaData($ctrl, $tables);
        if ($version = $schema->version) {
            $db = \IGK\System\Version::Parse($version);
            $db->release++;
            $n['version'] = $db . '';
        }
        $n['author'] = igk_getv($options, 'author') ?? IGK_AUTHOR;
        $src = igk_ob_get_func(function ($n) {
            echo $n->render();
        }, $n);
        if (empty($ofile = igk_getv($options, 'outputfile'))) {
            $ofile = $file;
        }
        return igk_io_w2file($ofile, $src);
    }
    /**
     * 
     */
    public static function BackupDataSchema(BaseController $ctrl, $defentries)
    {

        $tb = igk_db_get_ctrl_tables($ctrl);
        $schema = igk_html_node_dbdataschema();
        $apt = $ctrl->getDataAdapter();
        $appc = igk_getctrl(IGK_API_CTRL);
        if ($apt->connect()) {
            $entries = $schema->addNode(DbSchemas::ENTRIES_TAG);
            foreach ($tb as $v) {
                $rep = $schema->addNode(DbSchemas::DATA_DEFINITION)->setAttributes(array("TableName" => $v));
                if ($defentries)
                    $appc->datadb("get_table_definition", $rep, $v, $apt, null, $entries);
            }
            if (!$entries->HasChilds) {
                igk_html_rm($entries);
            }
            $apt->close();
        }
        return $schema;
    }

    /**
     * get link column name 
     * @param string $table 
     * @param mixed $column 
     * @param null|string $prefix 
     * @return string
     */
    public static function GetLinkColumn($columnInfo, $column, ?string $prefix = null)
    {
        // 
        $g = [$column];
        if ($prefix) {
            $np = self::TreatColumnName($column, $prefix);
            if ($np != $column)
                array_unshift($g, $np);
        }
        // get column table info
        while (count($g) > 0) {
            $q = array_shift($g);
            if (isset($columnInfo[$q])) {
                return $q;
            }
        }
        return null;
    }

    /**
     * treat value conditions
     * @param mixed $columns 
     * @param mixed $conditions 
     * @return array<string|int, mixed> 
     */
    public static function TreatSelectCondition($columns, $conditions, ?string $prefix = null)
    {
        $keys = array_keys($conditions);
        $count = 0;
        $conditions = array_map(function ($a) use ($columns, &$keys, &$count, $prefix) {
            $k = $keys[$count++];
            $r = [$k];
            if ($prefix && !igk_str_startwith($k, $prefix)) $r[] = $prefix . $k;
            $v = $a;
            while (count($r) > 0) {
                $k = array_shift($r);
                if (isset($columns[$k])) {
                    $keys[$count - 1] = $k;
                    $cl = $columns[$k];
                    if (preg_match("/date(time)?/i", $cl->clType) && is_string($a) && preg_match("/now\(\)/i", $a)) {
                        $v = new DbExpression($a);
                    }
                    break;
                }
            }
            return $v;
        }, $conditions);
        return array_combine($keys, array_values($conditions));
    }
    /**
     * get auto detected reversal column of table
     * @param string $table_name 
     * @return array<string, IDbColumnInfo>|false 
     * @throws IGKException 
     */
    public static function GetReversalUniqueColumn(string $table_name, bool $use_autoincrement = false)
    {
        $r = DbSchemas::GetTableColumnInfo($table_name);
        $reversal_col = [];
        foreach ($r as $k => $col) {
            if (!$use_autoincrement && $col->clAutoIncrement) continue;
            if ($col->clIsUnique) {
                $reversal_col[$k] = $col;
            }
        }
        if (count($reversal_col) > 0) {
            return $reversal_col;
        }
        return false;
    }
    /**
     * get reversal mapping link 
     * @param ModelBase $model 
     * @return mixed|<string,DbReverseMappingLink>
     * @throws IGKException 
     */
    public static function GetReversalMappingLink(ModelBase $model)
    {
        // load link definition if mandatory
        $r = null;
        $columns = $model->getTableColumnInfo();
        foreach ($columns as $k => $v) {
            if ($tb = $v->clLinkType) {
                $s = DbUtility::GetReversalUniqueColumn($tb, true);
                if ($s) {
                    if (is_null($r)) {
                        $r = [];
                    }
                    $f = new DbReverseMappingLink;
                    $f->columns = $s;
                    $f->table = $tb;
                    $f->model = DbCaches::GetTableInfo($tb)->model();

                    $r[$k] = $f;
                }
            }
        }
        return $r;
    }

    /**
     * preparent condition list to avoid duplicate
     * @param mixed $columns 
     * @param mixed $condition 
     * @return array 
     */
    public static function PreparateConditionsListToAvoidDuplicate($columns, $condition){
        $tab = [];
        $unique_columns = [];
        foreach($columns as $k=>$v){
            if ($v->clAutoIncrement) continue;
            if ((strtolower($v->clType) == 'guid') && !$v->clNotNull){
                continue;
            }
            $tv = igk_getv($condition, $v->clName);
            if ($v->clIsUnique) {
                if (!is_null($tv) || $v->clNotNull){                    
                    $tab[$k]=$tv;
                }
            }
            if ($v->clIsUniqueColumnMember){
                $idx = $v->clColumnMemberIndex ?? 0;
                if (!isset($unique_columns[$idx]))
                    $unique_columns[$idx] = [];
                if (!is_null($tv) || $v->clNotNull){                    
                    $unique_columns[$idx][$k] = igk_getv($condition, $v->clName);
                }
            }
        }
        if (count($tab)>1){
            $tab = [DbConditionExpressionBuilder::Create($tab, DbConditionExpressionBuilder::OP_OR)];
        }
        $reg = [];
        if (count($unique_columns)>0){
            foreach($unique_columns as $t){
                if(count($t)==0)continue;
                $keys = array_keys($t);
                sort($keys);
                $s = implode("-", $keys);
                if (isset($reg[$s]))
                    continue;
                $reg[$s] = 1;
                $tab[] = DbConditionExpressionBuilder::Create($t);

            }
        }

        return $tab;
    }
}
