<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SQLDataAdapter.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Database;

use IGK\System\Database\IDbSendQueryListener;
use IGK\System\Database\IDbSendQueryListenerSupport;
use IGK\System\Database\SQLGrammar;
use IGK\System\Html\IHtmlGetValue;
use IGKException;
use IGKSysUtil;
use ModelBase;

use function igk_getv as getv;
use function igk_resources_gets as __;

///<summary>Represente class: IGKSQLDataAdapter</summary>
/**
* Represente IGKSQLDataAdapter class
*/
abstract class SQLDataAdapter extends DataAdapterBase implements IIGKDatabaseCreator, IDbSendQueryListenerSupport{
    const DB_INFORMATION_SCHEMA = "information_schema";

    private $m_listener;

    public function setSendDbQueryListener(?IDbSendQueryListener $listener) {
        $this->m_listener = $listener; 
    }

    public function getSendDbQueryListener(): ?IDbSendQueryListener { return $this->m_listener; }
    /**
     * 
     * @param mixed $t 
     * @return mixed 
     * @throws IGKException 
     * @deprecated since 11.7.05.19 use SQLGrammar insteed
     */
    public static function ResolvType($t){        
        return SQLQueryUtils::ResolvType($t);
    }
    public function filterColumn($columninfo, $value): bool { 
        return false;
    }

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
     * 
     * @return SQLGrammar 
     */
    protected function create_grammar(){        
        $grammar = new SQLGrammar($this);
        // $grammar->driver = $this;
        return $grammar;
    }
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
        //+ insert and update function ignored
        return $cl; 
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    ///<param name="entry"></param>
    /**
    * 
    * @param mixed $tbname
    * @param mixed $condition
    */
    public function delete($tbname, $conditions=null){
        $query = $this->getGrammar()->createDeleteQuery($tbname, $conditions);		
        return $this->sendQuery($query); 
    }
    ///<summary>delete all from table</summary>
    /**
    * delete all from table
    */
    public function deleteAll($tbname, $condition=null){
        $query = $this->getGrammar()->createDeleteQuery($tbname, $condition);		
        return $this->sendQuery($query); 
    }
    ///<summary>setup manager config for next operation</summary>
    /**
    * setup manager config for next operation
    */
    protected function initConfig(){}
    ///<summary></summary>
    ///<param name="tbname"></param>
    ///<param name="values"></param>
    ///<param name="tableinfo" default="null"></param>
    /**
    * 
    * @param mixed $tbname table name
    * @param mixed $values value to insert 
    * @param mixed $tableinfo request table info
    * @param mixed $tableinfo the default value is null
    */
    public function insert($tbname, $values, $tableinfo=null, bool $throwException = true){
        $query = $this->getGrammar()->createInsertQuery($tbname, $values, $tableinfo);		
        return $this->sendQuery($query);  
    }


   ///<summary></summary>
    /**
    * 
    */
    public function last_id(){}
    ///<summary>build and send a mysql select query</summary>
    ///<param name="options">callback or igk_db_create_opt_obj()</param>
    /**
    * build and send a mysql select query
    * @param mixed $options callback or igk_db_create_opt_obj()
    * @return object query result
    */
    public function select($tbname, $where=null, $options=null, $throwex=false, $autoclose=false){
        $query = $this->getGrammar()->createSelectQuery($tbname, $where, $options);		
        return $this->sendQuery($query, $throwex, $options, $autoclose);   
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    /**
    * 
    * @param mixed $tbname
    */
    public function selectAll($tbname){
        $query = $this->getGrammar()->createSelectQuery($tbname);
        return $this->sendQuery($query, $tbname);
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    ///<param name="condition" default="null"></param>
    ///<param name="options" default="null"></param>
    /**
    * 
    * @param mixed $tbname
    * @param mixed $condition the default value is null
    * @param mixed $options the default value is null
    */
    public function selectAndWhere($tbname, $condition=null, $options=null){       
        if ($query = $this->getGrammar()->createSelectQuery($tbname, $condition, $options)){
            return $this->sendQuery($query, $tbname, $options);
        }
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    ///<param name="entry"></param>
    ///<param name="condition" default="null"></param>
    ///<param name="tabinfo" default="null"></param>
    /**
    * 
    * @param mixed $tablename
    * @param mixed $entry
    * @param mixed $condition the default value is null
    * @param mixed $tabinfo the default value is null
    */
    public function update($tablename, $entry, $condition=null, $tabinfo=null){
        // $this->dieNotConnect();
        $query = $this->getGrammar()->createUpdateQuery($tablename, $entry, $condition, $tabinfo);
        // $query=IGKSQLQueryUtils::GetUpdateQuery($tablename, $entry, $condition, $tabinfo);
        $s=$this->sendQuery($query, $tablename);
        return $s;
    }

    /**
     * 
     * @param mixed $type 
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
    public function getObjValue($value){
        
        if ($value instanceof \IGK\Models\ModelBase){
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