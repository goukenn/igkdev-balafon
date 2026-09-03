<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbUtility.php
// @date: 20230118 11:28:43
namespace IGK\System\Database\Helper;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Database\DbExpression;
use IGK\Database\DbSchemas;
use IGK\Database\IDbColumnInfo;
use IGK\Helper\Database;
use IGK\Models\ModelBase;
use IGK\System\Caches\DBCaches;
use IGK\System\Console\Logger;
use IGK\System\Database\DbConditionExpressionBuilder;
use IGK\System\Database\DbReverseMappingLink;
use IGK\System\Database\SQLGrammar;
use IGK\System\Database\SQLQueryFieldPrefixOperators;
use IGK\System\Html\Dom\HtmlNode;
use IGKException;
use IGKSysUtil;

/**
 * database helper utility class 
 * @package IGK\System\Database\Helper
 */
abstract class DbUtility
{
    /**
     * 
     * @param string $v 
     * @param mixed $grammar 
     * @return string|mixed 
     */
    public static function EscapeName(string $v, $grammar){
         if (preg_match('/^`.*`$/', $v)) {
            return $v;
        }
        if (strpos($v, ".") !== false) {
            $g = $grammar;
            return  $g::EscapeTableName($v, $g->driver());
        }
        return '`' . $v . '`';
    }
    /**
     * 
     * @param string $prefix 
     * @param mixed $row 
     * @return array 
     */
    public static function AutoPrefixColumn(string $prefix, $row){
        $trow = [];
        foreach($row as $k=>$v){
            $a = Database::AutoPrefixColumn($k, $prefix);
            $trow[$a] = $v;
        }
        return $trow;
    }
    /**
     * Escape slashes value for json detection.
     * @param string $value
     */
    public static function EscapeSlashesValueForJSonDetection(string $value)
    {
        return str_replace("/", "\\\\\\\\/", $value);
    }
    /**
     * prepare columnt list
     * @param array $columns
     * @param string $prefix
     * @var array
     */
    public static function PrepareColumnList(array $columns, string $prefix = IGK_FIELD_PREFIX): array
    {
        $user_tab_c = array_combine($columns, array_map(function ($a) use ($prefix) {
            $a = strtolower($a);
            $a = implode('.', array_slice(explode('.', $a), -1));
            return igk_str_rm_start($a, $prefix);
        }, $columns));
        return $user_tab_c;
    }
    /** 
     * column object 
     * @param array &$conditions 
     * @param mixed $columns 
     * @return void 
     */
    public static function TreatColumnsCondition(&$conditions, $columns)
    {
        if (!$conditions) return;
        foreach ($conditions as $k => $v) {
            if ($v instanceof ModelBase) {
                if (($clinfo = igk_getv($columns, $k)) && ($clinfo->clLinkType)) {
                    $cl = $clinfo->clLinkColumn ?? IGK_FD_ID;
                    $conditions[$k] = $v->{$cl};
                }
            }
        }
    }
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
    public static function RemoveColumnPrefixName(string $columnName, ?string $prefix)
    {
        if ($prefix) {
            $columnName = preg_replace("/^" . $prefix . "/i",  '', $columnName);
        }
        return $columnName;
    }
    /**
     * auto generate doc.
     * @param BaseController $ctrl
     * @param mixed $tables
     * @return mixed
     */
    public static function ExportToXMLSchemaData(BaseController $ctrl, $tables)
    {
        $xml = HtmlNode::CreateWebNode('dbdataschema');
        $bprefix = igk_configs()->db_prefix;
        $prefix = IGKSysUtil::DBReverseTableName($bprefix, $ctrl);
        foreach ($tables as $t => $v) {
            $rep = $xml->addNode(DbSchemas::DATA_DEFINITION)->setAttributes(array(
                "TableName" => $v->defTableName,
                "RefKey" => null,
                "Prefix" => $v->prefix,
                "Description" => $v->description,
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
        }
        return $xml;
    }
    /**
     * auto generate doc.
     * @param BaseController $ctrl
     * @param mixed $options
     * @return void
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
     * BackupData flatten schema
     * @param BaseController $ctrl 
     * @param ?array $defentries 
     * @return mixed 
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
     * @param mixed $columnInfo 
     * @param mixed $column 
     * @param null|string $prefix 
     * @return mixed|null 
     */
    public static function GetLinkColumn($columnInfo, $column, ?string $prefix = null)
    {
        $g = [$column];
        if ($prefix) {
            $np = self::TreatColumnName($column, $prefix);
            if ($np != $column)
                array_unshift($g, $np);
        }
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
     * @param ?string $prefix
     * @return array<string|int, mixed>
     */
    public static function TreatSelectCondition(array $columns, array $conditions, ?string $prefix = null)
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
     * @param bool $use_autoincrement
     * @throws IGKException
     * @return array<string, IDbColumnInfo>|false
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
     * @return mixed <string,DbReverseMappingLink>
     * @throws IGKException 
     */
    public static function GetReversalMappingLink(ModelBase $model)
    {
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
    public static function PreparateConditionsListToAvoidDuplicate($columns, $condition)
    {
        $tab = [];
        $unique_columns = [];
        foreach ($columns as $k => $v) {
            if ($v->clAutoIncrement) continue;
            if ((strtolower($v->clType) == 'guid') && !$v->clNotNull) {
                continue;
            }
            $tv = igk_getv($condition, $v->clName);
            if ($tv instanceof ModelBase) {
                $clinfo = $v->clLinkColumn;
                $tv = $tv->{$clinfo};
            }
            if ($v->clIsUnique) {
                if (!is_null($tv) || $v->clNotNull) {
                    $tab[$k] = $tv;
                }
            }
            if ($v->clIsUniqueColumnMember) {
                $idx = $v->clColumnMemberIndex ?? 0;
                if (is_object($idx))
                    $idx = (array)$idx;
                if (is_array($idx)) {
                    self::_LoadIndexColumns($unique_columns, $idx, $k, $tv);
                } else {
                    if (!isset($unique_columns[$idx]))
                        $unique_columns[$idx] = [];
                    if (!is_null($tv) || $v->clNotNull) {
                        $unique_columns[$idx][$k] = $tv;
                    }
                }
            }
        }
        if (count($tab) > 1) {
            $tab = [DbConditionExpressionBuilder::Create($tab, DbConditionExpressionBuilder::OP_OR)];
        }
        $reg = [];
        if (count($unique_columns) > 0) {
            foreach ($unique_columns as $t) {
                if (count($t) == 0) continue;
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
    private static function _LoadIndexColumns(array & $column_index, array $idx, string $column, $tv){
        foreach($idx as $index){
            if (!isset($column_index[$index])){
                $column_index[$index ] = [];
            }
            $column_index[$index ][$column] = $tv;
        }
    }
    /**
     * default map sys value
     * @param array $data 
     * @param string $prefix 
     * @return array<int|string, mixed> 
     */
    public static function MapSysValues(array $data, string $prefix = IGK_FIELD_PREFIX)
    {
        return array_combine(array_map(function ($a) use ($prefix) {
            $a = strtolower(self::RemoveColumnPrefixName($a, $prefix));
            return $a;
        }, array_keys($data)), array_values($data));
    }
    /**
     * auto generate doc.
     * @param ModelBase $model
     * @param ModelBase $link
     * @param string $model_column
     * @param string $link_column
     * @param array $conditions link model conditions
     * @return void
     */
    public static function CleanRereference(
        ModelBase $model,
        ModelBase $link,
        string $model_column,
        string $link_column,
        array $conditions
    ) {
        $rd = $link->get_query($conditions, ['Columns' => [$link_column]]);
        return $model->delete([
            SQLQueryFieldPrefixOperators::IN($model_column) => $rd
        ]);
    }
}
