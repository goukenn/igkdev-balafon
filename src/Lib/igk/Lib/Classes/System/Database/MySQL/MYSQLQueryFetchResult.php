<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MYSQLQueryFetchResult.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Database\MySQL;
use Exception;
use IGK\Database\DbConstants;
use IGK\Database\DbQueryResult;
use IGK\Database\DbSingleValueResult;  
use IGK\Database\DbQueryRowObj;
use IGK\Database\IDataDriver;
use IGK\Database\IDbQueryFetchResult;
use IGK\System\Polyfill\IteratorTrait;
use IGKSorter;
use IGK\IQueryResult;
use Iterator;
use ModelBase;
///<summary>implement fetch result/summary>
/**
*  implement fetch result
*/
final class MYSQLQueryFetchResult extends DbQueryResult  implements IQueryResult, IDbQueryFetchResult{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $init;
    /**
     * get or define resources options
     * @var mixed
     */
    var $options;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_query;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_rowcount;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_fieldcount;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_result;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_rowdef;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_columns = [];

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_tables = [];

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_model;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_driver;
    use IteratorTrait;
    // public function to_json($option = null, int $flag = 0) { }

    /**
    * auto generate doc.
    * @return ?array
    */
    public function to_array(): ?array {
        return null;// yield $this->fetch();
    }

    /**
    * auto generate doc.
    */
    public function generate(){
        return yield $this->fetch();
    }

    /**
    * auto generate doc.
    * @param mixed $index
    */
    public function getRowAtIndex($index) { 
        return null;
    }

    /**
    * auto generate doc.
    */
    protected function _iterator_key() { 
        return null;
    }

    /**
    * auto generate doc.
    * @param mixed $result
    */
    public function handle($result){
        $this->m_result = $result; 
        $this->m_fieldcount= igk_db_num_fields($result);
        $this->m_rowcount = igk_db_num_rows($result);
        $this->init = false;
    }

    /**
    * auto generate doc.
    */
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

    /**
    * auto generate doc.
    */
    public function getFieldCount(){
        return $this->m_fieldcount;
    }

    /**
    * auto generate doc.
    */
    public function getRowCount(){
        return $this->m_rowcount;
    }
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
    /**
    * 
    */

    public function getColumnCount(){
        return igk_count($this->m_columns);
    }
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
    /**
    * 
    */

    public function getColumns(){
        return $this->m_columns;
    }
    /**
    * 
    */

    public function getHasRow(){
        return ($this->getRowCount() > 0);
    }
    /**
    * retrieve the query
    */

    public function getQuery(){
        return $this->m_query;
    }
    /**
    * get the type of result. boolean|numeric|db_result
    */

    public function getResultType(){
        return "fetch";
    }   
    /**
    * 
    */

    public function getTables(){
        return $this->m_tables;
    }
    /**
    * get the request value
    */

    public function getValue(){
        return $this->m_value;
    }
    /**
     * fetch result
     * @return bool 
     * @throws Exception 
     */

    public function fetch():bool{
        //create and transform to db query row object
        $callback = $this->options ? igk_getv($this->options, DbConstants::CALLBACK_OPTS) : null;
        $this->m_rowdef = null;
        if ($v_tr = igk_db_fetch_assoc($this->m_result)){ 
            $v_otr = DbQueryRowObj::Create($v_tr);
            if ($callback){
                $callback($v_otr);
                $v_tr = $v_otr->to_array();
            }
            if ($this->m_model){
                $cl = $this->m_model;
                $this->m_rowdef = new $cl($v_tr);
            }else {
                $this->m_rowdef = $v_otr;
            }
        }
        return $this->m_rowdef !== null;
    }

    /**
    * auto generate doc.
    */
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

    /**
    * Used by var_dump() to customize debug output.
    */
    public function __debugInfo()
    {
        return [];
    }

    /**
    * auto generate doc.
    */
    public function _iterator_current(){
        return $this->m_rowdef;
    }

    /**
    * auto generate doc.
    */
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