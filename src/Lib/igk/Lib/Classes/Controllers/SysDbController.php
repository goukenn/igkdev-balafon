<?php
// @file: IGKSysDbController.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Controllers;
 
use IGK\System\Database\MySQL\Controllers\MySQLDataController; 
use IGKApp;
use IGKControllerManagerObject;

/**
 * system db controller
 * @package IGK\Controllers
 */
final class SysDbController extends NonVisibleControllerBase{
    ///<summary>Represente dropDb function</summary>
    protected static function dropDb(){
        $c=igk_getctrl(__CLASS__, false);
        if($c->getDataAdapterName() == IGK_MYSQL_DATAADAPTER){
            $sql=new MySQLDataController();
            $sql->drop_all_tables();
        }
    }
    ///<summary></summary>
    public function getDbConstantFile(){
        return igk_sys_db_constant_cache();
    }
    ///<summary>Represente getEntryNamespace function</summary>
    protected function getEntryNamespace(){
        return rtrim(IGK::class, "\\");
    }
    public function getClassesDir(){
        return IGK_LIB_CLASSES_DIR;
    }
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="cardinality"></param>
    ///<param name="type" default="1"></param>
    ///<param name="expression" default="(.)+"></param>
    public function getInfoDataEntry($name, $cardinality=0, $type=1, $expression="(.)+"){
        $utypeinfo=$this->getParam("m_userTypeInfo", array());
        return isset($utypeinfo[$name]) ? $this->m_userTypeInfo[$name]: array(
            IGK_FD_NAME=>$name,
            "clCardinality"=>$cardinality,
            "clType"=>$type,
            "clDataType"=>$expression
        );
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    public function & getm_userTypeInfo(){
        $r=$this->getParam("usertypeinfo");
        return $r;
    }
    ///<summary></summary>
    public function getName(){
        return IGK_SYSDB_CTRL;
    }
    ///<summary></summary>
    protected function getUseDataSchema(){
        return true;
    }
     
    // public function getDataTableInfo()
    // {
    //     return null; 
        // array(
        //     new DbColumnInfo(array(IGK_FD_NAME=>IGK_FD_ID, IGK_FD_TYPE=>"Int","clAutoIncrement"=>true,IGK_FD_TYPELEN=>10, "clIsUnique"=>true, "clIsPrimary"=>true)),
        //     new DbColumnInfo(array(IGK_FD_NAME=>"clBillId", IGK_FD_TYPE=>"Int", IGK_FD_TYPELEN=>10)),
        //     new DbColumnInfo(array(IGK_FD_NAME=>"clUId", IGK_FD_TYPE=>"Int", IGK_FD_TYPELEN=>10)),
        //     new DbColumnInfo(array(IGK_FD_NAME=>"clRefId", IGK_FD_TYPE=>"VarChar", IGK_FD_TYPELEN=>30)),
        //     new DbColumnInfo(array(IGK_FD_NAME=>"clQte", IGK_FD_TYPE=>"Int", IGK_FD_TYPELEN=>10)),
        //     new DbColumnInfo(array(IGK_FD_NAME=>"clAmount", IGK_FD_TYPE=>"Float", IGK_FD_TYPELEN=>10)),
        //     );
    // }
    public function getDataDir(){
        return IGK_LIB_DIR."/".IGK_DATA_FOLDER;
    }
    ///<summary>Represente Init function</summary>
    public static function Init(){
        igk_wln_e(__METHOD__);
        $c=igk_create_session_instance(__CLASS__, function(){
            $g=new self();
            IGKControllerManagerObject::getInstance()->registerController($g, !IGKApp::IsInit());
            return $g;
        });
        return $c;
    }

    public function __construct()
    { 
    }
    ///<summary></summary>
    protected function InitComplete(){
        parent::InitComplete();
        $this->RegValueTypeArray("USERTOKENID", null, 1, 1);
    }
    ///<summary></summary>
    protected static function initDb($force=false){
        if($c=igk_getctrl(static::class, false)){
            igk_set_env(IGK_ENV_DB_INIT_CTRL, $c);
            $c->initDbFromSchemas();
            $c->initDbConstantFiles();
            static::InitDataBaseModel($force);
            igk_set_env(IGK_ENV_DB_INIT_CTRL, null);
            return 1;
        }
        return false;
    }
    ///<summary>check if this class already be initialize </summary>
    public static function Initialized($cl){
        return false;
    }
    ///<summary>Represente IsFunctionExposed function</summary>
    ///<param name="func"></param>
    public function IsFunctionExposed($func){
        return igk_is_conf_connected();
    }
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="datatype" default="null"></param>
    ///<param name="cardinality"></param>
    ///<param name="nodb"></param>
    private function RegValueTypeArray($name, $datatype=null, $cardinality=0, $nodb=0){
        $tab=array(
            IGK_FD_NAME=>$name,
            "clDataType"=>$datatype,
            "clCardinality"=>$cardinality,
            "clType"=>$nodb
        );
        $utypeinfo=$this->getParam("m_userTypeInfo", array());
        $utypeinfo[$name]=$tab;
        $this->setUserTypeInfo($utypeinfo);
    }
    ///<summary></summary>
    ///<param name="t"></param>
    private function setUserTypeInfo($t){
        $this->setParam("usertypeinfo", $t);
    }
}
