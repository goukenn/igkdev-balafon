<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SQLDataAdapter.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Database;
use Exception;
use IGK\Database\IDatabaseCreator;
use IGK\System\Database\IDbSendQueryListener;
use IGK\System\Database\IDbSendQueryListenerSupport;
use IGK\System\Database\SQLGrammar;
use IGK\System\Html\IHtmlGetValue;
use IGKException;
use IGKSysUtil;
use ModelBase;
use function igk_getv as getv;
use function igk_resources_gets as __;

/**
* Represent IGKSQLDataAdapter class
*/
abstract class SQLDataAdapter extends DataAdapterBase implements IDatabaseCreator, IDbSendQueryListenerSupport{
    /**
    * Constant: db information schema.
    * @var mixed
    */
    const DB_INFORMATION_SCHEMA = "information_schema";
    /**
    * Listener: listener.
    * @var mixed
    */
    private $m_listener;
    /**
    * Sets Send Db Query Listener.
    * @param null|IDbSendQueryListener $listener
    */
    public function setSendDbQueryListener(?IDbSendQueryListener $listener) {
        $this->m_listener = $listener; 
    }
    /**
    * Returns Send Db Query Listener.
    * @return ?IDbSendQueryListener
    */
    public function getSendDbQueryListener(): ?IDbSendQueryListener { return $this->m_listener; }
    /**
    * auto generate doc.
    * @param mixed $t
    * @deprecated since 11.7.05.19 use SQLGrammar insteed
    * @return mixed
    */
    public static function ResolvType($t){        
        return SQLQueryUtils::ResolvType($t);
    }
    /**
    * Filters Column.
    * @param mixed $columninfo
    * @param mixed $value
    * @return bool
    */
    public function filterColumn($columninfo, $value): bool { 
        return false;
    }
    /**
     * create table format
     * @param null|array $options 
     * @return string 
     */
    public function getCreateTableFormat(?array $options=null):string{
        return "CREATE TABLE IF NOT EXISTS %s;";
    }
    /**
     * resolv driver parameter
     * @param string $k 
     * @param mixed $rowInfo 
     * @param mixed $tinfo 
     * @return null|string 
     * @throws IGKException 
     */
    public function getParam($k, $rowInfo=null, $tinfo=null): ?string{
        static $configs;
        if ($configs===null){
            $configs['auto_increment_word'] = "AUTO_INCREMENT";
        }
        $sys = $configs;
        if(empty($sys))
            return null;
        $m= getv($configs, $k);
        if(is_callable($m)){
            return $m($rowInfo, $tinfo);
        }
        return $m;
    }
     /**
     * create link expression
     * @param string $table table name
     * @param array $column 
     * @param array $value 
     * @param mixed $columnkey 
     * @return DbLinkExpression 
     */
    public function createLinkExpression($table, $column, $value, $columnkey){
        return new DbLinkExpression($table, $column, $value, $columnkey);      
    }
    /**
     * get grammar 
     * @return ?SQLGrammar 
     */
    public function getGrammar(){
        return $this->create_grammar() ?? die("grammar can't be found");
    }
    /**
    * auto generate doc.
    * @return SQLGrammar
    */
    protected function create_grammar(){        
        $grammar = new SQLGrammar($this);
        return $grammar;
    }
    /**
    * Escape.
    * @param null|string $str
    * @return string
    */
    public function escape(?string $str=null):string{
        return igk_db_escape_string($str) ?? '';
    }
    /**
     * get relation attached to table
     * @param mixed $adapter 
     * @param mixed $tname 
     * @return mixed 
     * @throws Exception 
     */
    protected static function GetRelation($adapter, $tname, $clname){
        $r = $adapter->getDbname();        
        $adapter->selectdb(static::DB_INFORMATION_SCHEMA); 
        $h=$adapter->sendQuery("SELECT * FROM `KEY_COLUMN_USAGE` WHERE `TABLE_NAME`='".igk_db_escape_string($tname)."' AND `TABLE_SCHEMA`='".igk_db_escape_string($r)."' AND `COLUMN_NAME`='".igk_db_escape_string($clname)."' AND `REFERENCED_TABLE_NAME`!=''");
        $adapter->selectdb($r);
        return $h->getRowAtIndex(0);
    }
    /**
    * Resolv column info.
    * @param mixed $adapter
    * @param mixed $table
    * @param mixed $columninfo
    */
    public static function ResolvColumnInfo($adapter, $table, $columninfo){
        $v = $columninfo;
        $table_n = $table;
        $mysql = $adapter;
        $cl= []; 
        $cl["clName"]=$v->Field;
        $tab=array();
        preg_match_all("/^((?P<type>([^\(\))]+)))\\s*(\((?P<length>([0-9]+))\)){0,1}( (?P<option>(unsigned)))?$/i", trim($v->Type), $tab);
        igk_ilog("name: ".$v->Field. " ".$v->Type);
        $cl["clType"]= $adapter->getGrammar()->ResolvType(igk_getv($tab["type"], 0, "Int"));
        $cl["clTypeLength"]=igk_getv($tab["length"], 0, 0);
        if (!empty($tab["option"][0])){
            switch(strtolower(trim($tab["option"][0]))){
                case "unsigned":
                    $cl["clType"] = "U".$cl["clType"];
                break;
            }
        }
        if($v->Default)
            $cl["clDefault"]=$v->Default;
        if($v->Comment){
            $cl["clDescription"]=$v->Comment;
        }
        $cl["clAutoIncrement"]=preg_match("/auto_increment/i", $v->Extra) ? "True": null;
        $cl["clNotNull"]=preg_match("/NO/i", $v->Null) ? "True": null;
        $cl["clIsPrimary"]=preg_match("/PRI/i", $v->Key) ? "True": null;
        $cl["clIsUnique"]=preg_match("/UNI/i", $v->Key) ? "True": null;
        if(preg_match("/(MUL|UNI)/i", $v->Key)){
            $rel= static::GetRelation($mysql, $table_n, $v->Field);
            if($rel){
                $cl["clLinkType"]=$rel->REFERENCED_TABLE_NAME;
                $cl["clLinkColumn"] = $rel->REFERENCED_COLUMN_NAME; 
                $cl["clLinkConstraintName"] = $rel->CONSTRAINT_NAME; 
            }
        }
        if (!empty($v->Extra) && (($cpos = strpos($v->Extra, "on update "))!==false)){
            $c = trim(substr($v->Extra, $cpos+10));
            if (in_array($c, ["CURRENT_TIMESTAMP"]))
                $cl["clUpdateFunction"] = "Now()";
        }
        return $cl; 
    }
    /**
    * auto generate doc.
    * @param mixed $condition
    */
    public function delete($tbname, $conditions=null){
        $query = $this->getGrammar()->createDeleteQuery($tbname, $conditions);		
        return $this->sendQuery($query); 
    }
    /**
    * delete all from table
    */
    public function deleteAll($tbname, $condition=null){
        $query = $this->getGrammar()->createDeleteQuery($tbname, $condition);		
        return $this->sendQuery($query); 
    }
    /**
    * setup manager config for next operation
    */
    protected function initConfig(){}
    /**
    * auto generate doc.
    * @param mixed $tableinfo the default value is null
    */
    public function insert($tbname, $values, $tableinfo=null, bool $throwException = true){
        $query = $this->getGrammar()->createInsertQuery($tbname, $values, $tableinfo);		
        return $this->sendQuery($query);  
    }
    /**
    * auto generate doc.
    */
    public function last_id(){}
    /**
    * build and send a mysql select query
    * @param mixed $options callback or igk_db_create_opt_obj()
    * @return object query result
    */
    public function select($tbname, $where=null, $options=null, $throwex=false, $autoclose=false){
        $query = $this->getGrammar()->createSelectQuery($tbname, $where, $options);		
        return $this->sendQuery($query, $throwex, $options, $autoclose);   
    }
    /**
    * auto generate doc.
    * @param mixed $tbname
    */
    public function selectAll($tbname){
        $query = $this->getGrammar()->createSelectQuery($tbname);
        return $this->sendQuery($query, $tbname);
    }
    /**
    * auto generate doc.
    * @param mixed $options the default value is null
    */
    public function selectAndWhere($tbname, $condition=null, $options=null){       
        if ($query = $this->getGrammar()->createSelectQuery($tbname, $condition, $options)){
            return $this->sendQuery($query, $tbname, $options);
        }
    }
    /**
    * auto generate doc.
    * @param mixed $tabinfo the default value is null
    */
    public function update($tablename, $entry, $condition=null, $tabinfo=null){
        $query = $this->getGrammar()->createUpdateQuery($tablename, $entry, $condition, $tabinfo);
        $s=$this->sendQuery($query, $tablename);
        return $s;
    }
    /**
    * auto generate doc.
    * @param mixed $value
    * @return string|null
    */
    public function getFuncValue($type, $value){
        switch($type){
            case "IGK_PASSWD_ENCRYPT":
            return "'".$this->escape_string(IGKSysUtil::Encrypt($value))."'";
        } 
        return null;
    }
    /**
    * auto generate doc.
    * @param mixed $value
    * @return mixed
    */
    public function getObjValue($value, ?string $for=null, $tableInfo = null){
        if ($value instanceof \IGK\Models\ModelBase){
            if ($for && $tableInfo){
                $clinfo = igk_getv($tableInfo, $for);
                $tk = $clinfo->clLinkColumn ?? IGK_FD_ID;
                return $value->{$tk};
            }
            return $value->id();
        } 
        if(igk_reflection_class_implement($value, IHtmlGetValue::class)){
            return $value->getValue(
                (object)[
                    "grammar"=>$this->getGrammar(),
                    "type"=>"insert"
                ]
            );
        }
        return null;
    }
    /**
    * Returns Ob Expression.
    * @param mixed $value
    * @param mixed $throwex
    */
    public function getObExpression($value, $throwex=false){
        if ($value instanceof DbExpression){
            return $value->getValue();
        } else {
            if ($throwex){
                throw new IGKException(__("objet not a DB Expression." .get_class($value)));
            }
        }
        return null;
    }
}