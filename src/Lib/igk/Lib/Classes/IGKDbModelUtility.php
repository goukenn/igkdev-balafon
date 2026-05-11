<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKDbUtility.php
// @date: 20220803 13:48:54
// @desc: base model utility class declaration 
use IGK\IDbUtility;
use function igk_resources_gets as __;

/**
* class used to manage database for a controller
*/
class IGKDbModelUtility extends IGKObject implements IDbUtility {
    /**
    * Property: ctrl.
    * @var mixed
    */
    private $m_Ctrl;
    /**
    * Property: ad.
    * @var mixed
    */
    private $m_ad;
    /**
    * Property: errorcode.
    * @var mixed
    */
    private $m_errorcode;
    /**
    * Property: errorstr.
    * @var mixed
    */
    private $m_errorstr;
    /**
    * Returns Hash Key.
    * @param mixed $v
    */
    protected function getHashKey($v){    
        if (is_bool($v)){
            return $v? "1" :"0";
        }
        if (is_object($v)){
            if (method_exists($v, "get_cachekey")){
                return $v->get_cachekey();
            }
            throw new IGKException("Object not implement get_cachekey");
        }
        return $v;
    }
    /**
    * Returns Cache Key.
    * @param mixed $n
    */
    protected function getCacheKey($n){
        if (is_array($n)){
            $o = [];
            foreach($n as $v){
                array_push($o, $this->getHashKey($n));
            }
            $n = implode("-", $n);
        }
        return $n;
    }
    /**
    * Cache.
    * @param mixed $name
    * @param callable $callback
    */
    public function cache($name, callable $callback){
        $key = "dbCache://".$this->getCacheKey($name);
        $args = array_slice(func_get_args(),2);
        if ($r = igk_environment()->get($key)){
            return $r;
        }
        $r =  call_user_func_array($callback, $args); 
        if ($r){
            igk_environment()->set($key, $r);
        }
        return $r;
    }
    /**
    * Last error.
    */
    public function last_error(){
        return $this->Ad->getError();
    }
    /**
     * select row
     * @param mixed $table 
     * @param mixed $conditions 
     * @param mixed $options 
     * @return mixed 
     * @throws IGKException 
     */
    public function select_row($table, $conditions, $options=null){
        return $this->selectSingleRow($table, $conditions, $options);
    }
    /**
    * auto generate doc.
    * @param mixed $name
    * @param mixed $arguments
    */
    public function __call($name, $arguments){
        if(preg_match("/Callback$/i", $name)){
            $fc=igk_getv($arguments, 0);
            $n=substr($name, 0, strlen($name)-8);
            if(!empty($n) && (strtolower($n) != "callback")){
                $s=call_user_func_array(array($this, $n), array_slice($arguments, 1));
                if($s !== null){
                    $fc($s);
                }
                return $s;
            }
        }
        $this->close(); 
        $msg="/!\\ DBUtility[".get_class($this)."] Action {$name} not implements";
        throw new \IGK\System\Exceptions\NotImplementException($msg);
    }
    /**
    * Selectdb.
    * @param mixed $dbname
    */
    public function selectdb($dbname){
        if ($this->m_ad)
            return $this->m_ad->selectdb($dbname);
        return false;
    }
    /**
    * auto generate doc.
    * @param mixed $ctrl
    */
    public function __construct($ctrl){
        if($ctrl == null){
            igk_die("variable ctrl can't be null");
        }
        $this->m_Ctrl=$ctrl;
        if($this->connect()){
            register_shutdown_function(function(){
                $this->close();
            });
        }      
    }
    /**
    * get table prefix
    */
    protected function _getTablePrefix(){
        return "";
    }
    /**
    * Returns Table Name.
    * @param mixed $table
    */
    public function getTableName($table){
		return igk_db_get_table_name($table);
	}
    /**
    * auto generate doc.
    * @param mixed $value
    */
    protected function _syncValue($value){
        $v_=$value;
        if(preg_match_all("#^@:/(?P<value>(.)+)$#", $value, $tab)){
            $v_=igk_getv($tab["value"], 0);
        }
        return $v_;
    }
    /**
    * table fixture
    * @param mixed $table
    */
    protected function _table($table){
        if(empty($table=trim($table))){
            igk_die("table name is empty");
        }
        return $this->_getTablePrefix().$table;
    }
    /**
    * auto generate doc.
    * @param mixed $callback
    */
    public function adCallback(callable $callback){
        if(!$this->connect())
            return;
        $o=$callback($this);
        $this->close();
        return $o;
    }
    /**
    * add direct object for table name
    * @param mixed $tablen
    * @param mixed $mixed
    */
    public function addObject($tablen, $mixed){
        $r=igk_db_create_row($tablen, $mixed);
        return $this->insertIfNotExists($tablen, $r);
    }
    /**
    * auto generate doc.
    */
    public function beginTransaction(){
        return $this->m_ad->beginTransaction();
    }
    /**
    * auto generate doc.
    * @param bool $leaveopen
    */
    public function close(bool $leaveopen=true){
        if($this->m_ad){
            $this->m_ad->close($leaveopen);
            $this->m_ad=null;
        }
    }
    /**
    * auto generate doc.
    */
    public function commit(){
        return $this->m_ad->commit();
    }
    /**
    * connect adapter
    */
    public function connect(){
        if(!$this->m_ad)
            $this->m_ad=$this->initDataAdapter();
        return $this->m_ad && $this->m_ad->connect($this->m_Ctrl);
    }
    /**
    * auto generate doc.
    * @param mixed $table
    * @param mixed $id the default value is null
    */
    public function delete($table, $id=null){
        if(is_numeric($id)){
            return igk_db_delete($this->m_Ctrl, $table, array(IGK_FD_ID=>$id));
        }
        return igk_db_delete($this->m_Ctrl, $table, $id);
    }
    /**
    * auto generate doc.
    * @param mixed $table
    */
    public final
    function dropTable($table){
        $this->connect();
        $r=null;
        if($this->m_ad){
            $r=$this->m_ad->dropTable($table);
        }
        $this->close();
        return $r;
    }
    /**
    * auto generate doc.
    * @param mixed $g
    */
    public function endTransaction($g){
        if($g){
            $this->m_ad->commit();
        }
        else{
            $this->m_ad->rollback();
        }
    }
    /**
    * get the data adapter
    */
    public final
    function getAd(){
        return $this->m_ad;
    }
    /**
    * auto generate doc.
    * @param mixed $table
    */
    public function getCanSyncDataTable($table){
        return true;
    }
    /**
    * get configs db function
    * @param mixed $n
    * @param mixed $default
    * @param mixed $table
    * @param mixed $comment
    */
    public function getConfigv($n, $default=null, $table=null, $comment=null){
        return igk_db_get_config($n, $default, $comment, 0);
    }
    /**
    * get controller
    */
    public function getCtrl(){
        return $this->m_Ctrl;
    }
    /**
    * auto generate doc.
    */
    public function getErrorCode(){
        return $this->m_errorcode;
    }
    /**
    * auto generate doc.
    */
    public function getErrorString(){
        return $this->m_errorstr;
    }
    /**
    * auto generate doc.
    * @param mixed $table
    * @param mixed $condition
    */
    public function getID($table, $condition){
        $r=$this->select($table, $condition)->getRowAtIndex(0);
        if($r)
            return $r->clId;
        return null;
    }
    /**
    * auto generate doc.
    */
    public function getLastQuery(){
        $ad=$this->Ad;
        return $ad ? $ad->getLastQuery(): -1;
    }
    /**
    * auto generate doc.
    * @param mixed $table
    * @param mixed $condition
    */
    public function getRow($table, $condition){
        $r=$this->select($table, $condition)->getRowAtIndex(0);
        return $r;
    }
    /**
    * auto generate doc.
    * @param mixed $table
    * @param mixed $id
    */
    public function getRowById($table, $id){
        return $this->select($table, array(IGK_FD_ID=>$id))->getRowAtIndex(0);
    }
    /**
    * return a sync data id
    * @param string $table
    * @param string $value
    * @param mixed $properties
    */
    public function getSyncDataID(string $table, string $value, $properties=null){
        if (($properties) && ($table == igk_db_get_table_name(IGK_TB_USERS))){
            if("+@id:/".$properties->User->clLogin === $value){
                return $properties->User->clId;
            }
            return $value;
        }
        $v_=$this->_syncValue($value);
        if(!empty($v_)){
            $tb_row=igk_getv($properties->Rows[$table], $v_);
            $c=$this->selectSingleRow($table, $tb_row["row"]);
            if($c){
                return $c->clId;
            }
            if($this->connect()){
                $id=null;
                if($this->insertIfNotExists($table, $tb_row["row"])){
                    $id=$this->last_id();
                    unset($properties->Entries[$table][$tb_row["index"]]);
                }
                else{
                    igk_ilog("Failed to insert value : ".$this->Ad->getLastQuery(), __FUNCTION__);
                }
                $this->close();
                return $id;
            }
        }
        return null;
    }
    /**
    * auto generate doc.
    * @param mixed $table
    * @param mixed $valueInTable
    * @param mixed $info the default value is null
    */
    public function getSyncDataValueDisplay($table, $valueInTable, $info=null){
        $row=$this->selectSingleRow($table, $valueInTable);
        if(!$row){
            return "[row is null ". $valueInTable."]";
        }
        if (($table == igk_db_get_table_name(IGK_TB_USERS)) && $row){
            return "+@id:/".$row->clLogin;
        }
        return "@:/".$this->getSyncIdentificationId($table, $row);
    }
    /**
    * auto generate doc.
    * @param mixed $table
    * @param mixed $syncrow
    */
    public function getSyncIdentificationId($table, $syncrow){
        $r=igk_getv($syncrow, "clId");
        if($r){
            return $r;
        }
        return igk_getv($syncrow, "clName");
    }
    /**
    * auto generate doc.
    * @param mixed $id
    */
    public function getSystemUserById($id){
        return igk_get_user($id); 
    }
    /**
    * get user id
    */
    public function getUID(){
        $u=$this->m_Ctrl->User;
        return $u ? $u->clId: 0;
    }
    /**
    * get user by id
    * @param mixed $uid
    */
    public function getUser($uid){
        return igk_get_user($uid);
    }
    /**
    * auto generate doc.
    * @param mixed $uid the default value is null
    */
    public function getUserId($uid=null){
        if(($uid == null) && ($u=$this->m_Ctrl->getUser())){
            $uid=$u->clId;
        }
        return $uid;
    }
    /**
    * initialize data adapter
    */
    protected function initDataAdapter(){
        return igk_get_data_adapter($this->m_Ctrl);
    }
    /**
    * auto generate doc.
    * @param mixed $table
    * @param mixed $obj
    */
    public function insert($table, $obj){
        $table=$this->_table($table);
        if($this->m_ad){
            return $this->m_ad->insert($table, $obj);
        }
        else
            return igk_db_insert($this->m_Ctrl, $table, $obj);
    }
    /**
    * Inserts array.
    * @param mixed $tbname
    * @param mixed $values
    * @param mixed $throwex
    */
    public function insert_array($tbname, $values, $throwex=true){
		 $tbname=$this->_table($tbname);
        if($this->m_ad){
            return $this->m_ad->insert_array($tbname, $values, $throwex);
        }
	}
    /**
    * auto generate doc.
    * @param mixed $table
    * @param mixed $obj
    * @param mixed $id
    */
    public function insertAndUpdate($table, $obj, $id='clId'){
        if($this->insert($table, $obj)){
            $obj->$id=$this->last_id();
            return 1;
        }
        return 0;
    }
    /**
    * auto generate doc.
    * @param string $table
    * @param mixed $obj
    * @param mixed $leaveOpen the default value is false
    */
    public function insertIfNotExists(string $table, $obj, $leaveOpen=false){
        return igk_db_insert_if_not_exists($this->m_Ctrl, $table, $obj, null, null, $leaveOpen, "Or");
    }
    /**
    * insert or update $obj
    * @param mixed $table
    * @param mixed $condition
    * @param mixed $obj
    * @param ?callable $callback
    */
    public function insertOrUpdate($table, $condition, $obj, ?callable $callback=null){
        $_invoke=function($r) use ($table, $condition, $obj, $callback){
            if($r->RowCount == 1){
                $row=$r->getRowAtIndex(0);
                if(is_callable($callback)){
                    if(!$callback($row, $obj))
                        return false;
                }
                $obj->clId=$row->clId;
                if($this->update($table, $obj, $condition)){
                    return 2;
                }
            }
            return igk_die("not implement ".$r->RowCount);
        };
        if($condition == null){
            $tab=null;
            if(igk_db_data_is_present($this->Ctrl, $table, $obj, null, $tab)){
                return $_invoke($tab);
            }
            if($this->insert($table, $obj))
                return 1;
        }
        else{
            $r=$this->select($table, $condition);
            if($r->RowCount > 0){
                return $_invoke($r);
            }
            else{
                if($this->insert($table, $obj))
                    return 1;
            }
        }
        return 0;
    }
    /**
    * auto generate doc.
    */
    public function last_id(){
        $ad=$this->Ad;
        return $ad ? $ad->last_id(): -1;
    }
    /**
    * auto generate doc.
    * @param mixed $tab
    * @param mixed $callback
    * @param mixed $tablen
    */
    public function loadCsvEntries($tab, $callback=null, $tablen="products"){
        $row=igk_db_create_row($tablen);
        if(!$row)
            return 0;
        return $this->adCallback(function($ad) use ($tab, $callback, $tablen, $row){
            $error=0;
            if($callback){
                foreach($tab as$v){
                    $error=!$callback($v, $tablen, $row, $ad) && !$error;
                }
            }
            else{
                foreach($tab as  $v){
                    $row->clName=igk_getv($v, 1);
                    $error=!$ad->insert($tablen, $row) && !$error;
                }
            }
            return !$error;
        });
    }
    /**
     * load model
     * @param mixed $modeltype 
     * @param mixed $name 
     * @return mixed 
     */
    public function model($modeltype, $name=null){
        return $this->Ctrl->loader->model($modeltype, $name);
    }
    /**
    * model rollback
    */
    public function rollback(){
        return $this->m_ad->rollback();
    }
    /**
    * auto generate doc.
    * @param mixed $table
    * @param mixed $condition
    * @param mixed $options the default value is null
    */
    public function select($table, $condition=null, $options=null){
        $table=$this->_table($table);
        if($this->m_ad){
            return $this->m_ad->select($table, $condition, $options);
        }
        igk_die("/!\ no adapter created. tips. call connect function first ");
        return igk_db_table_select_where($table, $condition, $this->m_Ctrl, false, $options);
    }
    /**
    * auto generate doc.
    * @param mixed $table
    * @param mixed $condition
    * @param mixed $callback the default value is null
    */
    public function selectCallback($table, $condition=null, $callback=null){
        $options=array("callback"=>$callback);
        $r=$this->select($table, $condition, $options);
        return $r;
    }
    /**
    * auto generate doc.
    * @param mixed $table
    * @param mixed $condition
    * @param mixed $options the default value is null
    */
    public function selectFirstRow($table, $condition=null, $options=null){
        $r=$this->select($table, $condition, $options);
        if($r && $r->RowCount > 0)
            return $r->getRowAtIndex(0);
        return null;
    }
    /**
    * auto generate doc.
    * @param mixed $table
    * @param mixed $condition
    * @param mixed $options the default value is null
    */
    public function selectLastRow($table, $condition=null, $options=null){
        $r=$this->select($table, $condition, $options);
        if($r && $r->RowCount > 0)
            return $r->getRowAtIndex($r->RowCount-1);
        return null;
    }
    /**
    * auto generate doc.
    * @param mixed $table
    * @param mixed $condition
    * @param mixed $options the default value is null
    */
    public function selectSingleRow($table, $condition=null, $options=null){
        $r=$this->select($table, $condition, $options);
        if($r && $r->RowCount == 1){
            $g=$r->getRowAtIndex(0);
            $g->{"sys:table"}=$table;
            return $g;
        }
        return null;
    }
    /**
    * auto generate doc.
    * @param mixed $querystring
    */    public final
    function sendQuery($querystring){
        $this->connect(); 
        $r=null;
        if($this->m_ad){
            $r=$this->m_ad->sendQuery($querystring);
        }
        $this->close();
        return $r;
    }
    /**
    * set the data adapter
    * @param mixed $ad
    */
    protected final
    function setAd($ad){
        $this->m_ad=$ad;
    }
    /**
    * auto generate doc.
    * @param mixed $code
    */
    protected function setErrorCode($code){
        $this->m_errorcode=$code;
    }
    /**
    * auto generate doc.
    * @param mixed $s
    */
    protected function setErrorString($s){
        $this->m_errorstr=$s;
    }
    /**
    * auto generate doc.
    * @param mixed $table
    */
    public final
    function tableExists($table): bool{
        return $this->getAd()->tableExists($table); 
    }
    /**
    * auto generate doc.
    * @param mixed $table
    * @param mixed $entrie
    * @param mixed $condition
    * @param mixed $tabinfo the default value is null
    */
    public final
    function update($table, $entrie, $condition=null, $tabinfo=null){
        $table=$this->_table($table);
        if ( $_ad=$this->getAd()){  
            return $_ad->update($table, $entrie, $condition, $tabinfo);
        }
        $r=null;
        if ($this->connect()){
            $_ad=$this->m_ad;
            if($_ad){            
                $r=$_ad->update($table, $entrie, $condition, $tabinfo);
            }
            $this->close();
        }
        return $r;
    }
    /**
    * Updates row.
    * @param mixed $row
    * @param null|mixed $table
    * @param null|mixed $condition
    */
    public function update_row($row, $table=null, $condition=null){
		($table == null) && !($table = $this->getTable()) && igk_die(__("table name not define"));
		return $this->update($table, $row, $condition, null);
	}
    /**
    * Selects rows.
    * @param null|mixed $table
    * @param null|mixed $condition
    * @param null|mixed $options
    */
    public function select_rows($table=null, $condition=null, $options=null){
		($table == null) && !($table = $this->getTable()) && igk_die(__("table name not define"));
		if ($g = $this->select($table, $condition, $options)){
			return $g->getRows();
		}
		return null;
	}
    /**
    * Drops.
    * @param mixed $table
    * @param null|mixed $condition
    */
    public function drop($table, $condition=null){
		$this->connect();
        $_ad=$this->Ad;
        if($_ad){ 
            $r=$_ad->drop($table, $condition);
        }
        $this->close();
    }
    /**
     * fix db utility
     * @param string? $condition 
     * @param string? $table 
     * @return mixed 
     * @throws IGKException 
     */
    public function select_count($condition=null, $table=null){
        if (!($table = $table ?? $this->getTable())){ 
            igk_die("table not found");
        }  
        if ($c = $this->ad->selectCount($table, $condition)){           
            if ($r = $c->getRowAtIndex(0)){
                return $r->count;  
            }
        }  
        return -1;
    }
}