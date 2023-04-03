<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MYSQLQueryFetchResult.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Database\MySQL;
 
use IGK\Database\DbQueryResult;
use IGK\Database\DbSingleValueResult;  
use IGK\Database\DbQueryRowObj;
use IGK\Database\IDataDriver;
use IGK\Database\IDbQueryFetchResult;
use IGK\System\Polyfill\IteratorTrait;
use IGKSorter;
use IIGKQueryResult;
use Iterator;
use ModelBase;

///<summary>implement fetch result/summary>
/**
*  implement fetch result
*/
final class MYSQLQueryFetchResult extends DbQueryResult  implements IIGKQueryResult, IDbQueryFetchResult{
    var $init;
    private $m_query;
    private $m_rowcount;
    private $m_fieldcount;
    private $m_result; 
    private $m_rowdef;
    private $m_columns = [];
    private $m_tables = [];
    private $m_model;
    private $m_driver;
    use IteratorTrait;

    public function to_array() {
        return yield $this->fetch();
    }

    public function getRowAtIndex($index) { 
        return null;
    }

    protected function _iterator_key() { 
        return null;
    }

    public function handle($result){
        $this->m_result = $result; 
        $this->m_fieldcount= igk_db_num_fields($result);
        $this->m_rowcount = igk_db_num_rows($result);
        $this->init = false;
    }
    protected function _iterator_valid(){
        return $this->m_rowdef !== null;
    }
    /**
     * check if query success
     * @return bool 
     */
    public function success():bool{
        return $this->m_rows !== null;
    }
    public function getFieldCount(){
        return $this->m_fieldcount;
    }
    public function getRowCount(){
        return $this->m_rowcount;
    }
    ///<summary></summary>
    /**
    * 
    */
    private function __construct(){       
    }
    /**
     * 
     * @param mixed $query 
     * @param IDataDriver $driver driver
     * @param \IGK\System\Database\MySQL\IGK\Models\ModelBase $model source model
     * @return MYSQLQueryFetchResult 
     */
    public static function Create($query, IDataDriver $driver, ?\IGK\Models\ModelBase $model=null){
        $c = new self();
        $c->m_query = $query;
        $c->m_model = $model;
        $c->m_driver = $driver; 
        return $c;
    }
    ///retult of the query  uses for boolean data
    /**
    */
    public function __toString(){
        return __CLASS__." [RowCount: ".$this->RowCount."]";
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getColumnCount(){
        return igk_count($this->m_columns);
    }
    ///<summary></summary>
    ///<param name="columnname"></param>
    /**
    * 
    * @param mixed $columnname
    */
    public function getColumnIndex($columnname){
        if(isset($this->m_columns[$columnname])){
            return $this->m_columns[$columnname]->index;
        }
        return -1;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getColumns(){
        return $this->m_columns;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getHasRow(){
        return ($this->getRowCount() > 0);
    }
    ///<summary></summary>
    /**
    * retrieve the query
    */
    public function getQuery(){
        return $this->m_query;
    }
   
    ///<summary>get the type of result. boolean|numeric|db_result</summary>
    /**
    * get the type of result. boolean|numeric|db_result
    */
    public function getResultType(){
        return "fetch";
    }   
  
    ///<summary></summary>
    /**
    * 
    */
    public function getTables(){
        return $this->m_tables;
    }
    ///<summary>get the request value</summary>
    /**
    * get the request value
    */
    public function getValue(){
        return $this->m_value;
    }
    
   

    public function fetch():bool{
        //create and transform to db query row object
        if ($this->m_rowdef = igk_db_fetch_assoc($this->m_result)){ 
            if ($this->m_model){
                $cl = $this->m_model;
                $this->m_rowdef = new $cl($this->m_rowdef);
            }else {
                $this->m_rowdef = DbQueryRowObj::Create($this->m_rowdef);
            }
        }
        return $this->m_rowdef !== null;
    }
    public function _iterator_rewind(){

        $dbresult = $this->m_result;
        if (!$dbresult)
            return false;
        if (!$this->init && $dbresult){
            $this->m_fieldcount= igk_db_num_fields($dbresult);
            $this->m_rowcount = igk_db_num_rows($dbresult);
            $this->init = true;
          
        }
        igk_db_seek($dbresult, 0);
        $this->fetch(); 
    }
    public function __debugInfo()
    {
        return [];
    }
    public function _iterator_current(){
        return $this->m_rowdef;
    }
    public function _iterator_next(){
        $this->fetch();
    }

    /**
     * 
     * @return null|object|DbQueryRowObj
     */
    public function row(): ?object{
        return $this->m_rowdef;
    }
}