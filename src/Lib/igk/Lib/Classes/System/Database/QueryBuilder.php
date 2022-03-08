<?php
namespace IGK\System\Database;

use IGK\Models\ModelBase; 
use IGK\Database\DbExpression;
use IGKQueryResult;

/**
 * use to build query
 * @package IGK\System\Database
 */
class QueryBuilder{
    private $m_conditions;
    private $m_options;
    private $model;


    public function __construct(ModelBase $model)
    {
        if (!$model)
            die("not allowed");
        $this->m_conditions = null;
        $this->m_options = [];
        $this->model = $model;
    }
   
    /**
     * help left join
     * @param mixed $condition 
     * @return array 
     */
    public static function LeftJoin($condition){
        return ["type"=>QueryBuilderConstant::LeftJoin, $condition];
    }
    public static function InnerJoin($condition){
        return ["type"=>QueryBuilderConstant::InnerJoin, $condition];
    }
    public static function Or(array $conditions){
        return (object)["operand"=>"OR", "conditions"=>$conditions];
    }
    /**
     * return a db expression 
     * @param mixed $string 
     * @return DbExpression 
     */
    public static function Expression($string){
        return new DbExpression($string);
    }

    /**
     * set conditions
     * @param array $condition 
     * @return $this 
     */
    public function conditions(array $condition){
        $this->m_conditions = $condition;
        return $this;
    }
    /**
     * set conditions
     * @param array $condition 
     * @return $this 
     */
    public function where(array $condition){
        return $this->conditions($condition);
    }
    /**
     * inner join table
     * @param array $join join table info \
     *  tableTable=>[condition : string(,[type=>"left|right"])] \
     *  to join multiple table, call join method for each table
     * @return $this 
     */
    public function join(array $join){
        $this->m_options["Joins"][] = $join;
        return $this;
    }
    public function join_left(string $table, string $condition){
        return $this->join([
            $table=>[$condition, "type"=>QueryBuilderConstant::LeftJoin]
        ]);
    }
    public function limit(array $limit_raw){
        $this->m_options["Limit"] = $limit_raw;
        return $this;
    }
    /**
     * set columns list
     * @param array $columnsList 
     * @return $this 
     */
    public function columns(?array $columnsList=null){
        $this->m_options["Columns"] = $columnsList;
        return $this;
    }
    /**
     * orderBy(["column|type",...])
     * @param array $order order condition with | separator
     * @return $this 
     * 
     */
    public function orderBy($order){
        $this->m_options["OrderBy"] = $order;
        return $this;
    } 
    public function distinct(bool $distinct=true){
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
    public function query(){  
        if (!isset($this->m_options["primaryKey"])){
            $this->m_options["NoPrimaryKey"] = 1;
        }  
        return $this->model->select_query($this->m_conditions, $this->m_options);
    }
    /**
     * get rows form request
     * @return mixed 
     */
    public function query_rows(){
        if ($r = $this->query()){
            return $r->getRows();
        }
        return null;
    }
    /**
     * retrieve the query to send
     * @return ?string 
     */    
    public function get_query(){
   
        if (!isset($this->m_options["primaryKey"])){
            $this->m_options["NoPrimaryKey"] = 1;
        }
        return $this->model->get_query($this->m_conditions, $this->m_options);
    }
    public function query_fetch(){
        $driver = $this->model->getDataAdapter();
        $query = $this->get_query();
        $res = $driver->createFetchResult($query, null);
        $options = $this->m_options;
        $driver->sendQuery($query, false, array_merge($options ?? [], [
            IGKQueryResult::RESULTHANDLER => $res
        ]));
        return $res; 
    }
    // 
}