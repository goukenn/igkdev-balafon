<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MySQLDataController.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Database\MySQL\Controllers;

///<summary>Represente class: IGKMySQLDataCtrl</summary>

use IGK\Controllers\BaseController;
use IGK\Models\DbModelDefinitionInfo;
use IGK\System\Controllers\Traits\NoDbActiveControllerTrait;
use IGK\System\Html\Dom\HtmlNode; 

/**
* Represente IGKMySQLDataCtrl class
*/
class MySQLDataController extends BaseController{
    use NoDbActiveControllerTrait;

    protected function getAutoGenerateModels(){
        return false;
    }
   
    ////!\ not realible
    ///<summar>/!\ delete all table from data base. return a node of</summary>
    /**
    */
    public function drop_all_tables(){
        $d=igk_get_data_adapter($this);
        $node=null;
        if($d->connect()){
            $node=igk_create_node("div");
            $r=$d->sendQuery("SHOW TABLES");
            $table=igk_html_build_query_result_table($r);
            $node->add($table);
            $dbname=$d->dbName;
            $tablelist=array();
            $deleted=array();
            foreach($r->Rows as $k=>$v){
                $i=$r->Columns[0]->name;
                $tablelist[$v->$i]=1;
                self::DropTableRelation($d, $v->$i, $dbname, $tablelist, $deleted);
            }
            $d->selectdb($dbname);
            $c=0;
            foreach($tablelist as $tbname=>$k){
                if(!$d->sendQuery("DROP Table IF EXISTS `".igk_db_escape_string($tbname)."` ")->success()){
                    $node->addNotifyBox("danger")->Content="Table ".$tbname. " not deleted ".igk_mysql_db_error();
                }
                $c++;
            }
            $d->selectdb($dbname);
            $d->close();
        }
        return $node;
    }
    ///<summary>remove all constraint attached to this database</summary>
    ///<param name="adapt"></param>
    ///<param name="dbname"></param>
    /**
    * drop all constraint attached to this database
    * @param mixed $adapt
    * @param mixed $dbname
    */
    public static function DropAllRelations($adapt, $dbname){
        $bck=$dbname;
        $adapt->selectdb("information_schema");
        $g=$adapt->sendQuery("DELETE FROM `TABLE_CONSTRAINTS` WHERE `TABLE_SCHEMA`='".igk_db_escape_string($dbname)."'");
        $adapt->selectdb($bck);
        return $g;
    }
    ///<summary></summary>
    ///<param name="adapt"></param>
    ///<param name="dbname"></param>
    ///<param name="qregex"></param>
    /**
    * 
    * @param mixed $adapt
    * @param mixed $dbname
    * @param mixed $qregex
    */
    public static function DropConstraints($adapt, $dbname, $qregex){
        $r=0;
        $g=0;
        $bck=$dbname;
        $adapt->selectdb("information_schema");
        $e=$adapt->sendQuery("SELECT * FROM `TABLE_CONSTRAINTS` WHERE `CONSTRAINT_NAME` LIKE '".igk_db_escape_string($qregex)."' AND `CONSTRAINT_SCHEMA`='".igk_db_escape_string($dbname)."'");
        $adapt->selectdb($bck);
        if($e && ($e->RowCount > 0)){
            $adapt->begintransaction();
            $r=1;
            foreach($e->Rows as  $v){
                $q="ALTER TABLE `".$v->TABLE_NAME."` DROP ".$v->CONSTRAINT_TYPE. " `".$v->CONSTRAINT_NAME."` ";
                $r=$r && $adapt->sendQuery($q);
            }
            if($r){
                $adapt->commit();
            }
            else{
                $adapt->rollback();
            }
        }
        $adapt->selectdb($dbname);
        return $r;
    }
    ///<summary>drop table</summary>
    ///<param name="tbname" type="mixed">mixed single table name or array of table name</param>
    /**
    * drop table
    * @param mixed tbname mixed single table name or array of table name
    */
    public static function DropTable($adapter, $tbname, $dbname, $node=null){
        if(is_array($tbname)){

            $tablelist=array();
            $deleted=array();
            foreach($tbname as $k=>$v){
                $i=$v;
                $tablelist[$i]=1;
                $deleted=array();
                self::DropTableRelation($adapter, $i, $dbname, $tablelist, $deleted, $node);
            }
            $adapter->selectdb($dbname);
            $r=true;
            $adapter->stopRelationChecking();
            foreach($tablelist as $ktbname=>$k){
                if (!
                    ($c = $adapter->sendQuery("Drop Table IF EXISTS `".igk_db_escape_string($ktbname)."`;")) || 
                    $c->success()
                ){
                    if($node)
                        $node->addNotifyBox("danger")->Content="Table ".$ktbname. " not deleted ".igk_mysql_db_error();
                    $r=false;
                }
            }
            $adapter->restoreRelationChecking();
            igk_hook(IGK_NOTIFICATION_DB_TABLEDROPPED, [$adapter, $tbname]);

        }
        else{
            $delete=null;
            self::DropTableRelation($adapter, $tbname, $dbname, null, $delete, $node);
            $g = $adapter->sendQuery("Drop Table IF EXISTS `".igk_db_escape_string($tbname)."` ");
            if(!$g || !$g->success()){
                igk_notifyctrl()->addErrorr("Table ".$tbname. " not deleted ".igk_mysql_db_error());
                return false;
            }
        }
        return true;
    }
    ///<summary></summary>
    ///<param name="adapter"></param>
    ///<param name="tbname"></param>
    ///<param name="dbname"></param>
    ///<param name="tablelist" default="null"></param>
    ///<param name="deleted" default="null" ref="true"></param>
    ///<param name="node" default="null"></param>
    /**
    * 
    * @param mixed $adapter
    * @param mixed $tbname
    * @param mixed $dbname
    * @param mixed $tablelist the default value is null
    * @param  * $deleted the default value is null
    * @param mixed $node the default value is null
    */
    public static function DropTableRelation($adapter, $tbname, $dbname, $tablelist=null, & $deleted=null, $node=null){
        $d=$adapter;
        $bck=$dbname;
       
        $rp = $d->selectdb("information_schema"); 
        $h=$d->sendQuery(
            "SELECT * FROM `TABLE_CONSTRAINTS` WHERE `TABLE_NAME`='".igk_mysql_db_tbname($tbname)."' AND `TABLE_SCHEMA`='".igk_db_escape_string($dbname)."';",
            true, null, false
        );
        // $g = $d->sendQuery("SELECT DATABASE() as dbName");
        // var_dump($g->getRows());
        // igk_wln_e("esources ", igk_environment()->get("mysql_resource"), 
        // "? = ".(igk_environment()->get("mysql_resource") === $d->getResId()) );
        // igk_wln_e($g->getRows());
        $d->selectdb($bck);
        $r=false;
        if($h && $h->RowCount > 0){
            $del=false;
            $ns="";
            foreach($h->Rows as $m=>$n){
                $ns=$n->CONSTRAINT_NAME;
                $nt=$n->CONSTRAINT_TYPE;
                switch($nt){
                    case "FOREIGN KEY":
                    if(!isset($deleted[$ns])){
                        $q="ALTER TABLE `".$n->TABLE_NAME."` DROP ".$nt." `".$ns."`";
                        if(!$d->sendQuery($q)->success()){
                            if($node)
                                $node->addNotifyBox("danger")->Content=$q." ".igk_mysql_db_error();
                        }
                        if($nt !== "FOREIGN KEY"){
                            $q="ALTER TABLE `".$n->TABLE_NAME."` DROP INDEX `".$ns."`";
                            if(!$d->sendQuery($q)->success()){
                                if($node)
                                    $node->addNotifyBox("danger")->Content=$q." ".igk_mysql_db_error();
                            }
                        }
                        $deleted[$n->CONSTRAINT_NAME]=1;
                    }
                    break;
                    case "PRIMARY KEY":
                    break;
                }
            }
        }
        return $r;
    }
    ///<summary></summary>
    ///<param name="adapt"></param>
    ///<param name="dbname"></param>
    /**
    * 
    * @param mixed $adapt
    * @param mixed $dbname
    */
    public static function GetAllRelations($adapt, $dbname){
        $bck=$dbname;
        $adapt->selectdb("information_schema");
        $g=$adapt->sendQuery("SELECT * FROM `TABLE_CONSTRAINTS` WHERE `TABLE_SCHEMA`='".igk_db_escape_string($dbname)."'");
        $adapt->selectdb($bck);
        return $g;
    }
    ///<summary></summary>
    ///<param name="a"></param>
    ///<param name="b"></param>
    ///<param name="tbase"></param>
    /**
    * 
    * @param mixed $a
    * @param mixed $b
    * @param mixed $tbase
    */
    public static function GetConstraint_Index($a, $b, $tbase){
        $bck=$tbase;
        $a->selectdb("information_schema");
        $h=$a->sendQuery("SELECT * FROM `TABLE_CONSTRAINTS` WHERE `TABLE_SCHEMA`='".$tbase."'");
        $i=1;
        $max=0;
        $ln=strlen($b);
        foreach($h->Rows as  $v){
            if(preg_match("/^".$b."/i", $v->CONSTRAINT_NAME)){
                $i++;
                $max=max($max, intval(substr($v->CONSTRAINT_NAME, $ln)));
            }
        }
        $a->selectdb($bck);
        return max($i, $max + 1);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getDataAdapterName():string{
        return IGK_MYSQL_DATAADAPTER;
    }
    ///<summary></summary>
    /**
    * 
    */
   
  
    ///<summary></summary>
    ///<param name="tbname"></param>
    /**
    * 
    * @param mixed $tbname
    */
    public function getEntries($tbname){
        $v=$this->getInfo($tbname);
        return ($v == null) ? null: $v->Entries;
    }
    ///<summary></summary>
    ///<param name="tbname"></param>
    /**
    * 
    * @param mixed $tbname
    */
    public function getInfo($tbname){
        return igk_getv($this->m_dictionary, $tbname);
    }
    ///<summary></summary>
    /**
    * not visible controller
    */
    public function getIsVisible():bool{
        return false;
    }
    protected function initTargetNode(): ?HtmlNode
    {
       //do nothing
       return null;
    }
     
    ///<summary></summary>
    ///<param name="adapt"></param>
    ///<param name="dbname"></param>
    ///<param name="e"></param>
    /**
    * 
    * @param mixed $adapt
    * @param mixed $dbname
    * @param mixed $e
    */
    public static function RestoreRelations($adapt, $dbname, $e){
        throw new \IGK\System\Exceptions\NotImplementException(__METHOD__); 
    }
 
}