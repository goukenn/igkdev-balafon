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

use IGK\System\Database\IDatabaseHost;
use IGK\System\Database\MySQL\Controllers\MySQLDataController;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\DeprecatedMethodException;
use IGKException;
use ReflectionException;

/**
 * system db controller
 * @package IGK\Controllers
 */
final class SysDbController extends NonVisibleControllerBase implements IDatabaseHost{
    public function getCanInitDb(){
        return true;
    }
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
        return rtrim(\IGK::class, "\\");
    }
    public function getClassesDir(){
        return IGK_LIB_CLASSES_DIR;
    }
    public function getArticlesDir()
    { 
        return IGK_LIB_DIR."/".IGK_ARTICLES_FOLDER;
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
    /**
     * get use of data schema
     * @return true 
     */
    public function getUseDataSchema():bool{
        return true;
    }
    
     
    public function getDataDir(){
        return IGK_LIB_DIR."/".IGK_DATA_FOLDER;
    }      
    ///<summary></summary>
    protected function initComplete($context=null){
        parent::initComplete();
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
