<?php
// @author: C.A.D. BONDJE DOUE
// @filename: QueryBuilder.php
// @date: 20220728 14:50:19
// @desc: 

namespace IGK\System\Database;

use Closure;
use IGK\Database\DbConstants;
use IGK\Models\ModelBase;
use IGK\Database\DbExpression;
use IGK\Database\DbQueryRowObj;
use IGK\Database\IDbQueryResult;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKException;
use IGKQueryResult;
use IGKSysUtil;
use ReflectionException;

/**
 * use to build query
 * @package IGK\System\Database
 */
class QueryBuilder
{
    private $m_conditions;
    private $m_options;
    private $m_model;
    private $m_with;
    private $m_withTotalCount;

    const JOINS = QueryOptions::JOINS;

    /**
     * field to add as total counter 
     * @param bool|string $value 
     * @return $this 
     */
    public function withTotalCount($value)
    {
        $this->m_withTotalCount = $value;
        return $this;
    }
    public function append_columns(?array $columns)
    {
        if (isset($this->m_options["Columns"]) && $columns) {
            $cl = &$this->m_options["Columns"];
            $cl += $columns;
        } else {
            if ($columns) {
                return $this->columns($columns);
            }
        }
        return $this;
    }
    /**
     * with expression to get single data value
     * @param mixed $table 
     * @param null|string $key 
     * @return $this 
     */
    public function with($table, ?string $key = null, ?bool $ignore_data = false)
    {
        if (!$this->m_with)
            $this->m_with = [];
        $cinfo = [
            "key" => $key ?? $table,
            "ignore_data" => $ignore_data
        ];
        $this->m_with[$table] = $cinfo;
        return $this;
    }


    public function __construct(ModelBase $model)
    {
        if (!$model)
            igk_die("model not allowed");
        $this->m_conditions = null;
        $this->m_options = [];
        $this->m_model = $model;
    }

    /**
     * help left join
     * @param mixed $condition 
     * @return array 
     */
    public static function LeftJoin($condition)
    {
        return ["type" => QueryBuilderConstant::LeftJoin, $condition];
    }
    public static function InnerJoin($condition)
    {
        return ["type" => QueryBuilderConstant::InnerJoin, $condition];
    }
    public static function Or(array $conditions)
    {
        return (object)["operand" => "OR", "conditions" => $conditions];
    }
    /**
     * return a db expression 
     * @param mixed $string 
     * @return DbExpression 
     */
    public static function Expression($string)
    {
        return new DbExpression($string);
    }

    /**
     * set conditions
     * @param array $condition 
     * @return static 
     */
    public function conditions(?array $condition = null)
    {
        $this->m_conditions = $condition;
        return $this;
    }
    /**
     * set conditions
     * @param array $condition 
     * @return static 
     */
    public function where(array $condition)
    {
        return $this->conditions($condition);
    }
    /**
     * inner join table
     * @param array $join join table info \
     *  tableTable=>[condition : string(,[type=>"left|right"], alias=>alias_name), ] \
     *  to join multiple table, call join method for each table
     * @return $this 
     */
    public function join(array $join)
    {
        foreach (array_keys($join) as $k) {
            if (is_numeric($k)) {
                igk_die("not a valid table key : " . $k);
            }
            $v = $join[$k];
            $this->m_options[self::JOINS][] = [$k => $v];
        }
        return $this;
    }
    /**
     * set joint left
     * @param string $table table name to joint
     * @param string $condition condition expression ... on ... for mysql
     * @return $this 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function join_left(string $table, string $condition, ?string $alias = null)
    {
        $rc = [$condition, "type" => QueryBuilderConstant::LeftJoin];
        if ($alias)
            $rc['alias'] = $alias;
        return $this->join([
            $table => $rc
        ]);
    }
    /**
     * helper: join left
     * @param string $table 
     * @param string $first_column 
     * @param string $second_column 
     * @return $this 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function join_left_on(string $table, string $first_column, string $second_column, ?string $alias = null)
    {
        return $this->join_left($table, sprintf("%s=%s", $first_column, $second_column), $alias);
    }
    public function join_table(string $table)
    {
        return $this->join([
            $table => ["type" => QueryBuilderConstant::Join]
        ]);
    }
    /**
     * limit query
     * @param null|array|int $limit_min
     * @param ?int $limit_max
     * @return $this 
     */
    public function limit($limit_raw, ?int $max = null)
    {
        if (!is_null($limit_raw)) {
            if (!is_array($limit_raw)) {
                !is_numeric($limit_raw) && igk_die("limit value not allowed");
                $limit_raw = [$limit_raw];
                if ($max) {
                    $limit_raw[] = $max;
                }
            }
        }
        $this->m_options[QueryOptions::LIMIT] = $limit_raw;
        return $this;
    }
    public function latest(?string $column = null)
    {
        $cl = $column;
        if (is_null($column)) {
            $cl = $this->model()->getPrimaryKey();
        }
        return $this->orderBy([$cl . "|DESC"]);
    }
    /**
     * set columns list
     * @param array $columnsList 
     * @return $this 
     */
    public function columns(?array $columnsList = null)
    {
        $this->m_options["Columns"] = $columnsList;
        return $this;
    }
    /**
     * orderBy(["column|type",...])
     * @param array $order order condition with | separator
     * @return $this 
     * 
     */
    public function orderBy(?array $order = null)
    {
        $this->m_options[QueryOptions::ORDER_BY] = $order;
        return $this;
    }
    /**
     * set callback filter
     * @param null|\callbable|Closure $filter 
     * @return $this 
     */
    public function filter(callable $filter = null)
    {
        $this->m_options[DbConstants::CALLBACK_OPTS] = $filter;
        return $this;
    }
    /**
     * add distinct flag - cause id of raw to reset 
     * @param bool $distinct 
     * @return $this 
     */
    public function distinct(bool $distinct = true)
    {
        if ($distinct)
            $this->m_options["Distinct"]  = 1;
        else
            unset($this->m_options["Distinct"]);
        return $this;
    }
    /**
     * send query
     * @return IGK\Models\IIGKQueryResult 
     */
    public function query()
    {
        if (!isset($this->m_options["primaryKey"])) {
            $this->m_options["NoPrimaryKey"] = 1;
        }
        return $this->m_model->select_query($this->m_conditions, $this->m_options);
    }
    /**
     * get rows form request
     * @return mixed 
     */
    public function query_rows()
    {
        if ($r = $this->query()) {
            return $r->getRows();
        }
        return null;
    }
    /**
     * retrieve the query to send
     * @return ?string 
     */
    public function get_query()
    {
        if (!isset($this->m_options["primaryKey"])) {
            $this->m_options["NoPrimaryKey"] = 1;
        }
        if ($s = $this->m_withTotalCount) {
            if (isset($this->m_options['Columns'])) {
                $this->m_options['Columns']['Count(*)'] = is_string($s) ? $s : "Count(*)";
            } else {
                $this->m_options['Columns'] = array_merge(
                    [
                        'Count(*)' => is_string($s) ? $s : "Count(*)",
                    ],
                    $tc = $this->model()->queryColumns()
                );
                $this->m_options[QueryOptions::GROUP_BY] = $tc; //['clGuid'.' '];
            }
        }
        return $this->m_model->get_query($this->m_conditions, $this->m_options);
    }
    /**
     * retrieve select sub query to send. remove the trailling ";"
     * @return string 
     */
    public function get_sub_query()
    {
        return rtrim($this->get_query(), " ;");
    }
    /**
     * 
     * @param null|object $options 
     * @return static 
     */
    public function setOptions($options = null)
    {
        $this->m_options = (array)$options;
        return $this;
    }
    /**
     * 
     * @return null|IGK\Database\IDbFetchResult 
     * @throws IGKException 
     */
    public function query_fetch()
    {
        $driver = $this->m_model->getDataAdapter();
        $query = $this->get_query();
        $res = $driver->createFetchResult($query, null, $this->m_model->getDataAdapter());
        $options = $this->m_options;
        $driver->sendQuery($query, false, array_merge($options ?? [], [
            IGKQueryResult::RESULTHANDLER => $res
        ]));
        return $res;
    }
    /**
     * execute the current builded query
     * @return bool|?IDbQueryResult result
     * @throws IGKException 
     */
    public function execute($throwOnError = true, $options = null, $autoclose = false)
    {
        $driver = $this->m_model->getDataAdapter();
        if (!empty($query = $this->get_query()) && $driver->connect()) {
            $n = $driver->getIsConnect() ? -1 : $driver->connect();
            $v_goptions = $options ?? $this->m_options;
            if (!empty($this->m_with)) {
                $old_callback = !$v_goptions ? null : igk_getv($v_goptions, IGKQueryResult::CALLBACK_OPTS);
                $options = [
                    IGKQueryResult::CALLBACK_OPTS => function ($v) use ($old_callback) {
                        $row = $v;
                        if ($old_callback && !($row = $old_callback($v))) {
                            return false;
                        }
                        return self::_BuildRefWith($v, $row, $this->model(), $this->m_with);
                    }
                ];
            } else {
                $options = $v_goptions;
            }
            $response =  $driver->sendQuery($query, $throwOnError, $options, false);
            if ((($n == -1) && $autoclose) || (($n != -1) && ($autoclose))) {
                $driver->close();
            }
            return $response;
        }
        return false;
    }
    /**
     * get referere links 
     * @param mixed $ref reference 
     * @param mixed $ctrl BaseController 
     * @param null|string $table 
     * @param string|null $property 
     * @return array[tablename, linkcolumn, source_table, require_property]
     */
    private static function _GetLinks(array $ref, $ctrl, ?string $table = null, string $property = null)
    {
        return array_filter(array_map(function ($a) use ($ctrl, $table, $property) {
            if (!$a->clLinkType)
                return null;
            return [IGKSysUtil::DBGetTableName($a->clLinkType, $ctrl), $a->clLinkColumn, $table, $property];
        }, $ref));
    }
    /**
     * 
     * @param mixed $v 
     * @param mixed $row 
     * @param mixed $model 
     * @param mixed $with 
     * @return mixed 
     */
    private static function _BuildRefWith($v, $row, $model, $with)
    {
        $tab = \IGK\Models\ModelBase::RegisterModels();
        $t_root = $model->getTable();
        if (isset($tab[$t_root])) {
            $ref = $tab[$t_root]->ref;
            $ctrl = $model->getController();
            if ($ref) {
                $links = self::_GetLinks($ref, $ctrl);
                $linktab = [$t_root => 1];
                self::_BuildRowDef($v, $row, $ctrl, $tab, $with, $links, $linktab);
            }
        }
        // foreach ($with as $k => $vv) {
        //     $w_table =
        //     $w_prop = $vv;
        //     if (is_array($vv)){
        //         $w_table =
        //         $w_prop = $vv['key'];
        //     }
        //     if (!is_numeric($k)) {
        //         $w_table = $k;
        //     }
        //     if (isset($tab[$w_table])) {
        //         $w_mod = $tab[$w_table]->model::model();
        //     }
        //     if (!isset($links[$w_table])) {
        //         $rlinks = self::_GetLinks($tab[$w_table]->ref, $ctrl, $w_table, $w_prop);
        //         $links = array_merge($links, $rlinks);
        //         $linktab[$w_table] = $vv;
        //     }
        // }
        // $w_table = null; 
        // $ref_column = null;
        // foreach ($links as $cl => $info) {
        //     list($table, $clname) = $info;
        //     $ctable = igk_getv($info, 2);
        //     $property = igk_getv($info, 3);
        //     if (key_exists($table, $linktab)){ //  == $w_table) {
        //         $w_mod = $tab[$table]->model::model();
        //         if ($dd = $v->$cl) {
        //             $clname = $clname ?? $w_mod->getPrimaryKey();
        //             $g = $w_mod::cacheRow([$clname => $dd]);
        //             $key =  $linktab[$table]['key'] ?? $clname ;
        //             $row->$key = $g;
        //         }
        //         // break;
        //     } else {
        //         if ($v->columnExists($cl) && $ctable) {
        //             // column exists
        //             $z_mod = $tab[$ctable]->model::model();
        //             $idcl = $ref_column ?? $z_mod->getPrimaryKey();
        //             $g = $z_mod::cacheRow([$idcl => $v->$idcl]);
        //             $row->$property = $g;

        //             if (is_array($with) && igk_getv($with[$ctable], 'ignore_data')){                        
        //                 foreach (array_keys($g->to_array()) as $mm){
        //                     unset($row->$mm);
        //                 }
        //             }
        //         }
        //     }
        // } 
        return $row;
    }
    private static function _BuildRowDef($v, $row, $ctrl, $tab, $with, $links, $linktab)
    {
        $row_defs = [$v];

        while (count($row_defs) > 0) {
            $v = array_shift($row_defs);

            foreach ($with as $k => $vv) {
                $w_table =
                    $w_prop = $vv;
                if (is_array($vv)) {
                    $w_table =
                        $w_prop = $vv['key'];
                }
                if (!is_numeric($k)) {
                    $w_table = $k;
                }
                if (isset($tab[$w_table])) {
                    $w_mod = $tab[$w_table]->model::model();
                }
                if (!isset($links[$w_table])) {
                    $rlinks = self::_GetLinks($tab[$w_table]->ref, $ctrl, $w_table, $w_prop);
                    $links = array_merge($links, $rlinks);
                    $linktab[$w_table] = $vv;
                }
            }
            $w_table = null;
            $columns_keys = [];

            // $ref_column = null;
            foreach ($links as $cl => $info) {
                list($table, $clname) = $info;
                // $ctable = igk_getv($info, 2);
                // $property = igk_getv($info, 3);
                if (key_exists($table, $linktab)) { //  == $w_table) {
                    $w_mod = $tab[$table]->model::model();
                    if ($dd = $v->$cl) {
                        if (igk_getv($linktab, $table) === 1) {
                            // resolved in value . ignore root value
                        } else {
                            $clname = $clname ?? $w_mod->getPrimaryKey();
                            $g = $w_mod::cacheRow([$clname => $dd]);
                            $key = ($linktab[$table]['key'] ?? $clname);
                            if ($tk = $row->$key){
                                // if (!is_array($tk)){
                                //     $tk = [$tk];
                                // } 
                                // $tk[] = $g;
                                // $tk = array_unique($tk); 
                                $columns_keys[$key][$cl] = $g;
                                $rm = (object)$columns_keys[$key];
                                $row->$key = (object)$columns_keys[$key]; // $tk;
                                // $bcl = array_search($columns_keys, $g);
                                //igk_wln_e("j :: ", $row);
                            }else{
                                $row->$key = $g;
                                $columns_keys[$key][$cl] = $g;
                                $row_defs[] = DbQueryRowObj::Create($g->to_array());
                            }
                        }
                    }
                    // break;
                } 
                //else {
                    // TODO: PROCESS LINK - TABLES 
                    //  if ($v->columnExists($cl) && $ctable) {
                    // column exists
                    // $z_mod = $tab[$ctable]->model::model();
                    // $idcl = $ref_column ?? $z_mod->getPrimaryKey();
                    // $g = $z_mod::cacheRow([$idcl => $v->$idcl], false);
                    // $row->$property = $g;

                    // if (is_array($with) && igk_getv($with[$ctable], 'ignore_data')){                        
                    //     foreach (array_keys($g->to_array()) as $mm){
                    //         unset($row->$mm);
                    //     }
                    // }
                    // }
                // }
            }
        }
    }
    /**
     * select single row from this
     * @return mixed 
     * @throws IGKException 
     */
    public function select_row()
    {
        if (($result = $this->query_fetch())
            && ($result->RowCount == 1)
            && ($result->fetch())
        ) {
            return $result->row();
        }
    }
    public function __toString()
    {
        return __CLASS__ . "[" . $this->get_query() . "]";
    }
    ///<summary>get model</summary>
    /**
     * get model
     * @return ModelBase 
     */
    public function model()
    {
        return $this->m_model;
    }
    // 
    /**
     * execute and get array
     * @return mixed 
     * @throws IGKException 
     */
    public function get()
    {
        if ($tab = $this->execute()) {
            return  $tab->getRows(); //->to_array();
        }
    }

    public function groupBy(?array $column = null)
    {
        $this->m_options[QueryOptions::GROUP_BY] = $column;
        return $this;
    }
}
