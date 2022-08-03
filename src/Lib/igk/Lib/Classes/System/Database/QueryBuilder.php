<?php
// @author: C.A.D. BONDJE DOUE
// @filename: QueryBuilder.php
// @date: 20220728 14:50:19
// @desc: 

namespace IGK\System\Database;

use IGK\Models\ModelBase;
use IGK\Database\DbExpression;
use IGKException;
use IGKQueryResult;
use QueryOptions;

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

    /**
     * with expression to get single data value
     * @param mixed $table 
     * @param null|string $key 
     * @return $this 
     */
    public function with($table, ?string $key=null){
        if (!$this->m_with)
            $this->m_with = [];
        $this->m_with[$table] = $key ?? $table;
        return $this;
    }


    public function __construct(ModelBase $model)
    {
        if (!$model)
            die("not allowed");
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
     * @return $this 
     */
    public function conditions(?array $condition=null)
    {
        $this->m_conditions = $condition;
        return $this;
    }
    /**
     * set conditions
     * @param array $condition 
     * @return $this 
     */
    public function where(array $condition)
    {
        return $this->conditions($condition);
    }
    /**
     * inner join table
     * @param array $join join table info \
     *  tableTable=>[condition : string(,[type=>"left|right"])] \
     *  to join multiple table, call join method for each table
     * @return $this 
     */
    public function join(array $join)
    {
        // igk_wln_e(array_keys($join));
        foreach(array_keys($join) as $k){
            if (is_numeric($k)){
                igk_die("not a valid table key : ".$k);
            }
            $v = $join[$k];
            $this->m_options["Joins"][] = [$k=>$v];
        }
        return $this;
    }
    public function join_left(string $table, string $condition)
    {
        return $this->join([
            $table => [$condition, "type" => QueryBuilderConstant::LeftJoin]
        ]);
    }
    /**
     * limit query
     * @param array|int $limit_min
     * @param ?int $limit_max
     * @return $this 
     */
    public function limit($limit_raw, ?int $max=null)
    {
        if (!is_array($limit_raw)){
            !is_numeric($limit_raw) && igk_die("value not allowed");
            $limit_raw = [$limit_raw];
            if ($max){
                $limit_raw[] = $max;
            }
        }   
        $this->m_options["Limit"] = $limit_raw;
        return $this;
    }
    public function latest(?string $column=null){
        $cl = $column;
        if (is_null($column)){
            $cl = $this->model()->getPrimaryKey();
        }
        return $this->orderBy([$cl."|DESC"]);
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
    public function orderBy(?array $order=null)
    {
        $this->m_options["OrderBy"] = $order;
        return $this;
    }
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
        return $this->m_model->get_query($this->m_conditions, $this->m_options);
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
    public function execute($throwOnError=true, $options=null)
    {
        $driver = $this->m_model->getDataAdapter();
        if (!empty($query = $this->get_query())) {
            igk_debug_wln($this->m_with);
            if (!empty($this->m_with)){
                $old_callback = !$options ? null : igk_getv($options, "@callback");
                $options = [
                    "@callback"=>function($v)use($old_callback){                        
                        $row = $v;
                        if ($old_callback && ! ($row = $old_callback($v))){
                            return false;
                        }
                        return self::_BuildRefWith($v, $row, $this->model(), $this->m_with);
                    }
                ];

            }
            return $driver->sendQuery($query, $throwOnError, $options);
        }
        return false;
    }
    private static function _BuildRefWith($v, $row, $model, $with){
        $tab = \IGK\Models\ModelBase::RegisterModels();
        $ref = $tab[$model->getTable()]->ref;
        $links = array_filter(array_map(function($a){ 
            if (!$a->clLinkType)
                return null;
            return [$a->clLinkType, $a->clLinkColumn]; 
        }, $ref));
     
        foreach($with as $k=>$vv){
            $w_table = $vv;
            $w_prop = $vv;
            if (!is_numeric($k)){
                $w_table = $k;
            }
            if (isset($tab[$w_table])){
                $w_mod = $tab[$w_table]->model::model();
            }
            foreach($links as $cl=>$info){
                list($table, $clname) = $info;
                if ($table == $w_table){
                    $clname = $clname ?? $w_mod->getPrimaryKey();                                  
                    if ($dd = $v->$cl){
                        $g = $w_mod::cacheRow([$clname=>$dd]); 
                        $row->$w_prop = $g;
                    }
                    break;
                }
            }
        }
        return $row;
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
    public function model(){
        return $this->m_model;
    }
    // 
    /**
     * execute and get array
     * @return mixed 
     * @throws IGKException 
     */
    public function get(){
        if ($tab = $this->execute()){
            return $tab->to_array();
        }
    }

    public function groupBy(?array $column=null){
        $this->m_options["GroupBy"] = $column;
        return $this;
    }
    

}
