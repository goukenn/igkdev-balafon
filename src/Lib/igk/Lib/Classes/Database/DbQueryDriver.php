<?php
///<summary>Represente class: DbQueryDriver</summary>
namespace IGK\Database;
use IGK\System\Database\MySQL\IGKMySQLQueryResult;
use IGK\System\Number;
use IGKEvents;
use IGKObject;
use IIGKdbManager;
use Throwable;

/**
* Represente DbQueryDriver class
*/
class DbQueryDriver extends IGKObject implements IIGKdbManager {
    private $fkeys;
    private $m_adapter;
    private $m_closeCallback;
    private $m_dbpwd;
    private $m_dbselect;
    private $m_dbport; // store the port
    private $m_dbserver;
    private $m_dbuser;
    private $m_isconnect;
    private $m_lastQuery;
    private $m_openCallback;
    private $m_openCount;
	private $m_dboptions;
    private $m_resource;
    private static $LENGTHDATA=array("int"=>"Int", "varchar"=>"VarChar");
    private static $__store;
    private static $sm_resid;
    public  static $Config;
    static $idd=0;
    const DRIVER_MYSQLI = "MySQLI";
    public function getServer(){
        return $this->m_dbserver;
    }
    public function getUser(){
        return $this->m_dbuser;
    }
    public function getPort(){
        return $this->m_dbport;
    }
    public function getPwd(){
        return $this->m_dbpwd;
    }
    ///<summary>.ctr</summary>
    /**
    * .ctr
    */
    private function __construct(){}
    ///<summary></summary>
    ///<param name="tablename"></param>
    ///<param name="entries"></param>
    ///<param name="forceload"></param>
    /**
    * 
    * @param mixed $tablename
    * @param mixed $entries
    * @param mixed $forceload the default value is 0
    */
    private function __initTableEntries($tablename, $entries, $forceload=0){
        if(!$forceload && igk_get_env("pinitSDb")){ 
            igk_reg_hook(IGKEvents::HOOK_DB_INIT_ENTRIES, array(new DbEntryToLoad($this, $tablename, $entries), "loadEntries"));
            return;
        }
        // igk_db_load_entries($this, $tablename, $entries);
    }
    ///<summary></summary>
    ///<param name="query"></param>
    /**
    * 
    * @param mixed $query
    * @param mixed $option null or array key of object 
    */
    private function _sendQuery($query, $options=null){
        return $this->getSender()->sendQuery($query, $options);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function __sleep(){
        if($this->m_openCount > 1){
            igk_die("open count must not be greather than 1");
        }
        return array();
    }

  
    ///<summary></summary>
    /**
    * 
    */
    public function __wakeup(){
        igk_wln_e("wake up not allowed ", $this->m_openCount, get_class($this));
        // $this->m_resource=null;
        // $this->m_openCount=0;
    }
    ///<summary></summary>
    ///<param name="leaveOpen" default="false"></param>
    /**
    * 
    * @param mixed $leaveOpen the default value is false
    */
    public function close($leaveOpen=false){
        if($this->getIsConnect()){
            if($leaveOpen && ($this->m_openCount == 1)){
                return;}
            $this->m_openCount--;
            if($this->m_openCount<=0){
                if(igk_db_is_resource($this->m_resource))
                    igk_mysql_db_close($this->m_resource);
                $this->m_isconnect=false;
                $this->m_resource=null;
                self::SetResId(null, __FUNCTION__);
                $this->m_openCount=0;
            }
        }
    }
    ///<summary></summary>
    /**
    * 
    */
    public function closeAll(){
        if(igk_db_is_resource($this->m_resource)){
            igk_mysql_db_close($this->m_resource);
            $this->m_isconnect=false;
            $this->m_resource=null;
            $this->m_openCount=0;
        }
        if($this->m_closeCallback){
            call_user_func_array($this->m_closeCallback, array());
        }
    }
	public function escape_string($v){
		return igk_db_escape_string($v);
	}
    ///<summary></summary>
    /**
    * 
    */
    public function connect(){
    
        if($this->m_isconnect && $this->m_resource){
            if(@$this->m_resource->ping()){
                self::SetResId($this->m_resource, __FUNCTION__);
                $this->m_openCount++;
                igk_set_env("lastCounter", igk_ob_get_func('igk_trace'));
                return true;
            }
            $lcount=$this->m_openCount;
            $this->m_openCount=0;
            $this->m_isconnect= false;
            $this->m_openCount=0;
            igk_die("[igk] The connection was not closed properly :::ping failed : ".
				$lcount." <br />");
				
        }
        $r=igk_db_connect($this);
    
        if(igk_db_is_resource($r)){
            self::SetResId($r, __FUNCTION__);

            $t=igk_db_query("SELECT SUBSTRING_INDEX(CURRENT_USER(),'@',1)");
            if($t && (igk_db_num_rows($t) == 1)){
                $this->m_isconnect=true;
                $this->m_resource=$r;
                $this->m_openCount=1;
              
                return true;
            }
        }
        else{
            $_error=__CLASS__."::Error : SERVER RESOURCE # ";
            igk_notify_error($_error, "sys");
        }
        self::SetResId(null, __FUNCTION__);
        $this->m_isconnect=false;
        $this->m_resource=null;
        return false;
    }
    ///<summary></summary>
    ///<param name="dbserver"></param>
    ///<param name="dbname"></param>
    ///<param name="dbuser"></param>
    ///<param name="dbpwd"></param>
    /**
    * 
    * @param mixed $dbserver
    * @param mixed $dbname
    * @param mixed $dbuser
    * @param mixed $dbpwd
    */
    public function connectTo($dbserver, $dbname, $dbuser, $dbpwd){
        if($this->m_isconnect)
            $this->close();
        $r=@\igk_db_connect($dbserver, $dbuser, $dbpwd);
        if(\igk_db_is_resource($r)){
            $t=\igk_db_query("SELECT SUBSTRING_INDEX(CURRENT_USER(),'@',1)");
            if($t && (\igk_db_num_rows($t) == 1)){
                $this->m_isconnect=true;
                $this->m_resource=$r;
                $this->m_openCount=1;
                $tt=$this->selectdb($dbname);
                if(!$tt){
                    igk_debug_wln("DB Not selected : ". $dbname);
                }
                return $tt;
            }
        }
        else{
            $s=igk_mysql_db_error();
            igk_notifyctrl()->addError("MySQLError  : ".$s);
            igk_debug_wln("ERROR : ". $s);
        }
        $this->m_isconnect=false;
        $this->m_resource=null;
        return false;
    }
    ///<summary></summary>
    ///<param name="dbserver" default="localhost"></param>
    ///<param name="dbuser" default="root"></param>
    ///<param name="dbpwd" default=""></param>
    /**
    * 
    * @param mixed $dbserver the default value is "localhost"
    * @param mixed $dbuser the default value is "root"
    * @param mixed $dbpwd the default value is ""
    */
    public static function Create($dbserver="localhost", $dbuser="root", $dbpwd="", $port = null){
        $out=new DbQueryDriver();
        if (is_object($dbserver)){
            //principal info 
            $out->m_dbserver=trim($dbserver->server);
            $out->m_dbuser=trim($dbserver->user);
            $out->m_dbpwd=trim($dbserver->pwd);
            $out->m_dbport = $dbserver->port;
        } else {
            $out->m_dbserver= trim($dbserver);
            $out->m_dbuser=trim($dbuser);
            $out->m_dbpwd=trim($dbpwd);
            $out->m_dbport = $port;
        }
        $out->connect();
        if($out->m_isconnect){
            if (igk_environment()->is("DEV")  && !empty($db = igk_app()->Configs->db_name)){
                $out->createDb($db);
            }
            $out->close();
        }
        else{
            $out=null;
        }
        return $out;
    }
    ///<summary></summary>
    ///<param name="db"></param>
    /**
    * 
    * @param mixed $db
    */
    public function createdb($db){
        if(!$this->getIsConnect())
            return false;
        // + | autocreate database 
        if (igk_environment()->is("DEV") || igk_app()->getConfigs()->db_auto_create){
            $this->getSender()->sendQuery("CREATE DATABASE IF NOT EXISTS `".$this->escape_string($db)."` ");
        }
        return true;
    }
    ///<summary>create table</summary>
    /**
    * create table
    */
    public function createTable($tbname, $columninfo, $entries=null, $desc=null, $dbname=null){
        if(!$this->getIsConnect())
            return false; 
        if ($grammar = $this->m_adapter->getGrammar()){
            $query = $grammar->createTableQuery($tbname, $columninfo, $desc, $dbname);
            if ($this->sendQuery($query)){
                if ($entries){
                    $this->m_adapter->pushEntries($tbname, $entries, $columninfo);
                }
                igk_hook(IGK_HOOK_DB_TABLECREATED, [$this, $tbname]);
                return true;
            }
        }
        return null;

        // $v_rs=false;
        // $v_infkey="sys://db/tabfinfo/data";
        // $v_tableinit_info=igk_get_env($v_infkey);
        // $queryfilter = igk_environment()->mysql_query_filter;

        // if($v_tableinit_info === null){
        //     $v_tableinit_info=array(
        //         "__linkdata"=>array(),
        //         "__tables"=>array(),
        //         "__failed"=>array(),
        //         "__created"=>array(),
        //         "__constraint"=>0
        //     );
        //     igk_get_env($v_infkey, $v_tableinit_info);
        // }
        // $dbname=$dbname == null ? igk_sys_getconfig("db_name"): $dbname;
        // $tbname=igk_mysql_db_tbname($tbname);
        

        // $query=IGKSQLQueryUtils::CreateTableQuery($tbname, $columninfo, $desc, $this->m_adapter);
        // $nk=igk_get_env("sys://db/constraint_key");
        // if($nk){
        //     if(!igk_get_env("sys://db/initConstraint/".$nk)){
        //         IGKMySQLDataCtrl::DropConstraints($this->m_adapter, $dbname, $nk."%");
        //         igk_set_env("sys://db/initConstraint/".$nk, 1);
        //     }
        // }

        // $t=$this->getSender()->sendQuery($query);
        // if($t){
        //     igk_hook(IGK_HOOK_DB_TABLECREATED, [$this, $tbname]);
        //     $v_tableinit_info["__created"][$tbname]=$tbname;
        //     $query="";
        //     $tlinks=array();
        //     $linkdata=igk_getv($v_tableinit_info, "__linkdata");
        //     $c=& $v_tableinit_info["__tables"];
        //     foreach($columninfo as $k=>$v){
        //         if($v->clLinkType != null){
        //             $v_tableinit_info["__constraint"]++;
        //             $ck_index=$v_tableinit_info["__constraint"];
        //             $nk=igk_get_env("sys://db/constraint_key", "ctn_");
        //             if($ck_index == 1){
        //                 $idx=1;
        //                 $ck_index=max($idx, $ck_index);
        //                 $v_tableinit_info["__constraint"]=$idx++;
        //             }
        //             $nk=strtolower($nk.$ck_index);
        //             if(strlen($nk) > 64){
        //                 $tbm=explode("_", $nk);
        //                 $tbs="";
        //                 foreach($tbm as $_rs){
        //                     $tbs .= !empty($_rs) ? $_rs[0]: "";
        //                 }
        //                 $nk="constraint_".$tbs;
        //             }
        //             $nk = $queryfilter ? '' : "`".$nk."`";
        //             $query=IGKString::Format("ALTER TABLE {0} ADD CONSTRAINT {1} FOREIGN KEY (`{2}`) REFERENCES {3}  ON DELETE RESTRICT ON UPDATE RESTRICT;\n",
		// 				"`{$dbname}`.`{$tbname}`", 
        //                 $nk,
        //                  $v->clName, IGKString::Format("`{0}`.`{1}`(`{2}`)",
		// 				$dbname,
		// 				$v->clLinkType,
		// 				igk_getv($v, "clLinkColumn", IGK_FD_ID)
		// 			));
        //             $sender=$this->getSender();
        //             if($this->tableExists($v->clLinkType)){
        //                 $t=$this->_sendQuery($query);
        //             }
        //             else{
        //                 if(!isset($tlinks[$v->clLinkType])){
        //                     $mm=igk_getv($linkdata, $v->clLinkType);
        //                     if($mm == null){
        //                         $mm=(object)array("To"=>1, "from"=>array($tbname=>1));
        //                     }
        //                     else{
        //                         $mm->To++;
        //                         $mm->from[$tbname]=1;
        //                     }
        //                     $tlinks[$v->clLinkType]=$mm;
        //                     $linkdata[$v->clLinkType]=$mm;
        //                 }
        //                 else{
        //                     $tlinks[$v->clLinkType]->To++;
        //                     $tlinks[$v->clLinkType]->from[$tbname]=1;
        //                 }
        //                 $inf=null;
        //                 if(isset($v_tableinit_info[$v->clLinkType])){
        //                     $inf=$v_tableinit_info[$v->clLinkType];
        //                 }
        //                 else{
        //                     $inf=array();
        //                 }
        //                 $inf[]=$query;
        //                 $v_tableinit_info[$v->clLinkType]=$inf;
        //             }
        //         }
        //     }
        //     $direct=true;
        //     if($entries != null){
        //         if(igk_count($tlinks) > 0){
        //             $c[$tbname]=array("links"=>$tlinks, "entries"=>$entries);
        //             $direct=false;
        //         }
        //         else{
        //             $this->__initTableEntries($tbname, $entries, 1);
        //         }
        //     }
        //     else{
        //         $ctrl=igk_get_env(IGK_ENV_DB_INIT_CTRL) ?? igk_die(__("Environment failed : current controller to init not found. ".$tbname));
        //         if(igk_count($tlinks) == 0){
        //             igk_hook(IGKEvents::HOOK_DB_DATA_ENTRY, [$this, $tbname, 0]);
        //         }
        //         else{
        //             $c[$tbname]=array(
        //                 "links"=>$tlinks,
        //                 "entries"=>null,
        //                 "callback"=>function() use ($tbname){
        //                         igk_hook(IGKEvents::HOOK_DB_DATA_ENTRY, [$this, $tbname, 0]);
        //                     }
        //             );
        //         }
        //     }
        //     if(isset($v_tableinit_info[$tbname])){
        //         $error=false;
        //         foreach($v_tableinit_info[$tbname] as $k=>$v){
        //             if(!$this->_sendQuery($v)){
        //                 $msg="Alter failed : ". igk_debuggerview()->getMessage();
        //                 $error=true;
        //                 $v_tableinit_info["__failed"][]=$v;
        //             }
        //         }
        //         unset($v_tableinit_info[$tbname]);
        //         igk_debug_wln("init table with link ".$tbname);
        //         if(isset($linkdata[$tbname])){
        //             $tt=$linkdata[$tbname]->from;
        //             foreach($tt as $x=>$y){
        //                 if(isset($c[$x])){
        //                     unset($c[$x]["links"][$tbname]);
        //                     if(igk_count($c[$x]["links"]) == 0){
        //                         $e=$c[$x]["entries"];
        //                         if($e)
        //                             $this->__initTableEntries($x, $e);
        //                         else{
        //                             $cb=igk_getv($c[$x], "callback");
        //                             if(igk_is_callable($cb)){
        //                                 call_user_func_array($cb, array($this, $tbname));
        //                             }
        //                             else{
        //                                 igk_ilog(__METHOD__, " no callback found for ".$tbname);
        //                                 igk_ilog($cb);
        //                                 igk_die("no callback error");
        //                             }
        //                         }
        //                         unset($c[$x]);
        //                     }
        //                 }
        //             }
        //             unset($linkdata[$tbname]);
        //         }
        //     }
        //     foreach($c as $k=>$v){
        //         $m=$v["links"];
        //         if(isset($m[$tbname])){
        //             unset($m[$tbname]);
        //             if(igk_count($m) == 0){
        //                 $e=$v["entries"];
        //                 $this->__initTableEntries($k, $e);
        //                 unset($v["links"]);
        //                 unset($v["entries"]);
        //                 unset($v_tableinit_info["__tables"][$k]);
        //             }
        //             else{
        //                 $v["links"]=$m;
        //             }
        //         }
        //     }
        //     $v_tableinit_info["__linkdata"]=$linkdata;
        //     $v_tableinit_info["__tables"]=$c;
        //     $v_rs=true;
        // }
        // if(!$v_rs)
        //     igk_debug_wln("failed to create ".$tbname);
        // igk_set_env("sys://db/tabfinfo/data", $v_tableinit_info);
        // return $v_rs;
    }
    ///delete item in tables
    /**
    */
    public function delete($tbname, $values=null){
        $this->dieNotConnect();
        if ($this->m_adapter->delete($tbname, $values)){
            if ($values==null){
                $this->m_adapter->sendQuery("ALTER TABLE `".$tbname."` AUTO_INCREMENT =1");
            }
            return true;
        }
        return false;
        // $query=IGKSQLQueryUtils::GetDeleteQuery($tbname, $values);
        // $sender=$this->getSender();
        // $t=$this->_sendQuery($query);
        // if($t){
        //     if($values == null)
        //         $this->_sendQuery("ALTER TABLE `".$tbname."` AUTO_INCREMENT =1");
        //     return true;
        // }
        // return false;
    }
    ///<summary>delete all items</summary>
    /**
    * delete all items
    */
    public function deleteAll($tbname, $condition=null){
        $sender=$this->getSender();
        $this->dieNotConnect();
        $tbname= $this->escape_string($tbname);
        return $this->m_adapter->delete($tbname, $condition); 
    }
    ///<summary></summary>
    ///<param name="t"></param>
    ///<param name="msg" default=""></param>
    /**
    * 
    * @param mixed $t
    * @param mixed $msg the default value is ""
    */
    protected function dieinfo($t, $msg=""){
 
        if(!$t){ 
            $d=$this->getErrorCode();
            $m = $em=$this->getError(); 
            if (!igk_is_cmd()){
                $m="<div><div class=\"igk-title-4 igk-danger\" >/!\\ ".__CLASS__." Error</div><div>". $em."</div>"."<div>Code: ".$d."</div>"."<div>Message: <i>".$msg."</i></div></div>";
            } else {
                $m = implode(PHP_EOL, ["code: $d", "query: ".$this->getLastQuery(), "error: $m"]);
            }
            $this->ErrorString=$em;
            switch($d){
                case 1062:
                case 1146:
                return null;
            }
            igk_push_env("sys://adapter/sqlerror", $m);
            if(!igk_sys_env_production()){
                throw new \Exception($m);
            }
        }
    }
    ///<summary></summary>
    /**
    * 
    */
    function dieNotConnect(){
        // if (igk_env_count(__METHOD__)>9){
        // igk_trace();
        // igk_exit();
        // }
        // if (igk_environment()->get("base")){
        //     igk_wln_e("already call");
        // }
        // igk_environment()->set("base", 1);
       // echo "conn <br />";
        // igk_trace();
        try{
        if(!$this->getIsConnect()){
            igk_trace();
            igk_die("/!\\ DB Not connected");
        }
        } catch(Throwable $ex){
            igk_wln_e("bind");
        }
        // igk_environment()->set("base", null);
    }
    ///<summary></summary>
    ///<param name="tablename"></param>
    /**
    * 
    * @param mixed $tablename
    */
    public function dropTable($tablename){
        igk_die(__METHOD__." not implement");
    }
    ///<summary></summary>
    /**
    * 
    */
    public function flushForInitDb($complete=null){
        $v_infkey="sys://db/tabfinfo/data";
        $v_tableinit_info=igk_get_env($v_infkey);
        $ad=$this->m_adapter;
        $tb=$v_tableinit_info ? igk_getv(igk_getv($v_tableinit_info, "__failed"), 0): [];
        $tbs=$v_tableinit_info ? igk_getv($v_tableinit_info, "__linkdata"): [];
        if ($ad->connect()){

        
        if(igk_count($tbs) > 0 ){
            foreach($tbs as $k=>$v){
                $queries=$v_tableinit_info[$k];
                foreach($queries as $q){
                    $ad->sendQuery($q);
                }
            } 
        }
        $sender=$this->getSender();
        $tbs=igk_getv($v_tableinit_info, "__linkdata");
        if((igk_count($tbs) > 0) && $ad->connect()){
            foreach($tbs as $k=>$v){
                $queries=$v_tableinit_info[$k];
                foreach($queries as $q){
                    $this->_sendQuery($q);
                }
            } 
        }
        if(is_array($tb) && (igk_count($tb) > 0)){
            igk_debug_wln("send failed table .... creation ");
            foreach($tb as $k=>$v){
                $sender->endQuery($v);
            }
        }
        if (is_callable($complete)){
            $complete();
        }
        $ad->close();
        }
        if(($dg=igk_debuggerview()) && ($msg=$dg->getMessage())){
            igk_wln_assert(!empty($msg), $msg);
            return !empty($msg);
        }
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getdatabases(){
        if(!$this->getIsConnect())
            return;
        $t=$this->getSender()->sendQuery("SHOW DATABASES");
        return $t;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getDbServer(){
        return $this->m_dbServer;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getDbUser(){
        return $this->m_dbUser;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getError(){
        return igk_mysql_db_error();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getErrorCode(){
        return igk_mysql_db_errorc();
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="throwError" default="1"></param>
    /**
    * 
    * @param mixed $n
    * @param mixed $throwError the default value is 1
    * @return mixed|object|resource|mysqli object result or resources
    */
    public static function GetFunc($n, $throwError=1){
        $tn=self::$Config["db"];
        if(empty($tn))
            return null;
        $fc=igk_getv(self::$Config[$tn]["func"], $n);
        if(empty($fc))
            $fc=null;
        return $fc ?? ($throwError ? igk_die("no <b>{$n}</b> found in {$tn} dataadapter "): null);
    }
    ///<summary>get if last execution has an error</summary>
    /**
    * get if last execution has an error
    */
    public function getHasError(){
        return igk_mysql_db_has_error();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getIsConnect(){
        return $this->m_isconnect;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getLastQuery(){
        return $this->m_lastQuery;
    }
    ///<summary></summary>
    ///<param name="tablename"></param>
    ///<param name="name"></param>
    /**
    * 
    * @param mixed $tablename
    * @param mixed $name
    */
    public function getNewContraintKeys($tablename, $name){
        if($this->fkeys == null)
            $this->fkeys=array();
        $s="csk_". ((strlen($name) > 3) ? substr($name, 0, 3): $name).Number::ToBase(count($this->fkeys) + 1, 16, 4);
        $this->fkeys[]=$s;
        return $s;
    }
    ///<summary></summary>
    /**
    * 
    */
    public static function GetResId(){
        return self::$sm_resid;
    }
    ///<summary></summary>
    /**
    * 
    */
    private function getSender(){
        return $this->m_adapter ?? $this;
    }
    ///<summary></summary>
    ///<param name="k"></param>
    ///<param name="rowInfo" default="null"></param>
    ///<param name="tinfo" default="null" ref="true"></param>
    /**
    * 
    * @param mixed $k
    * @param mixed $rowInfo the default value is null
    * @param mixed * $tinfo the default value is null
    */
    public static function GetValue($k, $rowInfo=null, & $tinfo=null){
        $sys=self::$Config["db"];
        if(empty($sys))
            return null;
        $m=igk_getv(self::$Config[$sys], $k);
        if(igk_is_callable($m)){
            return $m($rowInfo, $tinfo);
        }
        return $m;
    }
    ///<summary></summary>
    ///<param name="tabname"></param>
    ///<param name="ctrl" default="null"></param>
    /**
    * 
    * @param mixed $tabname
    * @param mixed $ctrl the default value is null
    */
    public function haveNoLinks($tabname, $ctrl=null){
        $v_infkey="sys://db/tabfinfo/data";
        $v_tableinit_info=igk_get_env($v_infkey);
        $c=& $v_tableinit_info["__tables"];
        if(isset($c[$tabname])){
            if($ctrl != null){
                $c[$tabname]["callback"]=function() use($tabname){
                    igk_hook(IGKEvents::HOOK_DB_DATA_ENTRY, [$this, $tabname, 0]);
                };
            }
            return 0;
        }
        return 1;
    }
    ///<summary></summary>
    ///<param name="callback"></param>
    /**
    * 
    * @param mixed $callback
    */
    public static function Init($callback){
        if(self::$Config == null)
            self::$Config=array();
        $callback(self::$Config);
    }
    ///<summary></summary>
    /**
    * 
    */
    public static function InitDefault(){
        $dbName = igk_get_env("dbName");
        $db=new DbQueryDriver();
        $db->connect();
        $db->selectdb($dbName);
        return $db;
    }
    ///<summary>reset db initialize algorithm algorithm</summary>
    /**
    * reset db initialize algorithm algorithm
    */
    public function initForInitDb(){
        igk_set_env("sys://db/tabfinfo/data", null);
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    ///<param name="values"></param>
    ///<param name="tableinfo" default="null"></param>
    /**
    * 
    * @param mixed $tbname
    * @param mixed $values
    * @param mixed $tableinfo the default value is null
    */
    public function insert($tbname, $values, $tableinfo=null){
        $this->dieNotConnect();
        $tableinfo=$tableinfo == null ? igk_db_getdatatableinfokey($tbname): $tableinfo;
        igk_wln_e("get class:", get_class($this->m_adapter));
        return $this->m_adapter->insert($tbname, $values, $tableinfo);
        // igk_wln(__FILE__.':'.__LINE__, ["tbname"=>$tbname, "table info"=>$tableinfo]);
        // IGKSQLQueryUtils::SetAdapter($this);
        // $query=IGKSQLQueryUtils::GetInsertQuery($tbname, $values, $tableinfo);
        // $t=$this->getSender()->sendQuery($query);
        // if($t){
        //     if(($t->getResultType() == "boolean") && $t->getValue()){
        //         if(is_object($values)){
        //             if(igk_getv($values, IGK_FD_ID) == null)
        //                 $values->clId=$this->lastId();
        //         }
        //         return true;
        //     }
        //     return false;
        // }
        // else{
        //     $error="[IGK] - Insertion Query Error : ".igk_mysql_db_error(). " : ".$query;
        //     igk_ilog($error);
        //     igk_db_error($error);
        // }
        // return false;
    }
    ///<summary></summary>
    /**
    * 
    */
    public static function IsMySQLi(){
        return self::Is(self::DRIVER_MYSQLI);
    }
    ///<summary>get if the current state is on driver</summary>
    public static function Is($driverName){
        $s= self::$Config["db"];
        return ($s == strtolower($driverName));
        // DRIVER_MYSQLI
    }

    ///get the last inserted id
    /**
    */
    public function lastId(){
        return igk_mysql_db_last_id();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function OpenCount(){
        return $this->m_openCount;
    }
    ///<summary></summary>
    /**
    * 
    */
    public static function RestoreConfig(){
        DbQueryDriver::$Config=self::$__store;
        self::$__store=null;
    }
    ///<summary></summary>
    ///<param name="cbinfo"></param>
    /**
    * 
    * @param mixed $cbinfo
    */
    public static function SaveConfig($cbinfo){
        $ctn=array_merge(DbQueryDriver::$Config);
        $g=DbQueryDriver::$Config["func"];
        DbQueryDriver::$Config["db"]="user";
        $tab=array("escapestring"=>function($v) use ($cbinfo){
                    return $cbinfo->escapeString($v);
                });
        DbQueryDriver::$Config["func"]=$tab;
        self::$__store=$ctn;
    }
    ///<summary></summary>
    ///<param name="table"></param>
    ///<param name="cond" default="null"></param>
    ///<param name="options" default="null"></param>
    /**
    * 
    * @param mixed $table
    * @param mixed $cond the default value is null
    * @param mixed $options the default value is null
    */
    public function select($table, $cond=null, $options=null){
        $this->dieNotConnect();
        return $this->m_adapter->select($table, $cond, $options);
        // $query=IGKSQLQueryUtils::GetSelectQuery($this, $table, $cond, $options);
        // $s=$this->_sendQuery($query, $options);
        // return $s;
    }
    ///<summary></summary>
    ///<param name="dbname"></param>
    /**
    * 
    * @param mixed $dbname
    */
    function selectdb($dbname){
        $this->dieNotConnect();
        $mysql_func=self::GetFunc("selectdb");
        if(self::$Config["db"] == "mysqli"){
            if($this->m_resource){
                if(!@$this->m_resource->ping())
                    return false;
                return $mysql_func($this->m_resource, $dbname);
            }
            return false;
        }
        else{
            return $mysql_func($dbname);
        }
    }
    ///<summary></summary>
    /**
    * 
    */
    public function selectLastId(){
        return IGKMySQLQueryResult::CreateResult($this->_sendQuery("SELECT  LAST_INSERT_ID()"));
    }
    ///<summary></summary>
    ///<param name="query"></param>
    ///<param name="throwex" default="true"></param>
    /**
    * 
    * @param mixed $query
    * @param mixed $throwex the default value is true
    */
    public function sendQuery($query, $throwex=true){
        $v_qdebug = igk_environment()->querydebug;
        $v_nolog = false;
        if (is_array($throwex)){
            $v_nolog = igk_getv($throwex, "no_log", false);
            $throwex = igk_getv($throwex, "throw");
        } 
        
        if(igk_db_is_resource($this->m_resource)){
			if ($v_qdebug){ 
                igk_dev_wln($query);
				igk_push_env(IGK_ENV_QUERY_LIST, $query);
			}
            $this->setLastQuery($query);
            $t=igk_db_query($query, $this->m_resource);
            if (!$t && !$v_nolog){ 
                igk_ilog("Query Error:".$this->getError()."\n".$query."\n"); 
            }            
            if($throwex){
                $this->dieinfo($t, "/!\\ SQL Query Error :<div style='font-style:normal;'>".igk_html_query_parse($query)."</div>");
            }
            else if(!$t)
                return null;
            return $t;
        }
        return null;
    }

	public function sendMultiQuery($query, $throwex=true){
        $v_qdebug = igk_environment()->querydebug;
		if(igk_db_is_resource($this->m_resource)){
            $this->setLastQuery($query);
            if ($v_qdebug){
                igk_dev_wln($query);
				igk_push_env(IGK_ENV_QUERY_LIST, $query);
			}
            $t = igk_db_multi_query( $query , $this->m_resource);

            if($throwex){
                $this->dieinfo($t, "/!\\ SQLQuery Error:<div style='font-style:normal;'>".igk_html_query_parse($query)."</div>");
            }
            else if(!$t)
                return null;
            return $t;
        }
        return null;
	}
    ///<summary></summary>
    ///<param name="o"></param>
    /**
    * 
    * @param mixed $o
    */
    public function setAdapter($o){
        $this->m_adapter=$o;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setCloseCallback($v){
        $this->m_closeCallback=$v;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    private function setLastQuery($v){
        $this->m_lastQuery=$v;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setOpenCallback($v){
        $this->m_openCallback=$v;
    }
    ///<summary></summary>
    ///<param name="r"></param>
    ///<param name="context" default="null"></param>
    /**
    * 
    * @param mixed $r
    * @param mixed $context the default value is null
    */
    private static function SetResId($r, $context=null){
        self::$sm_resid=$r;
    }
    ///<summary></summary>
    ///<param name="tablename"></param>
    /**
    * 
    * @param mixed $tablename
    */
    public function tableExists($tablename){
        if(empty($tablename))
            return false;
        try {
            
            $s=$this->_sendQuery("SELECT Count(*) FROM `".igk_mysql_db_tbname($tablename)."`", 
                [
                    "throw"=>false,
                    "no_log"=>true
                ]
            );
            if($s && ($s->ResultType == "boolean")){
                return true;  
            } 
            return $s !== null;
        }
        catch(\Exception $ex){
        }
         return false;
    }
    ///update data table
    /**
    */
    public function update($tbname, $entry, $where=null, $querytabinfo=null){
        igk_wln($tbname);
        igk_trace();
        igk_exit();
        $this->dieNotConnect();
        return $this->m_adapter->update($tbname, $entry, $where, $querytabinfo);         
    }
}