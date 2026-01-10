<?php
// @file: IGKSysDbController.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
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
    protected static function dropDb($navigate=1, $force=0, $clean=false){
        $c=igk_getctrl(__CLASS__, false);
        if($c->getDataAdapterName() == IGK_MYSQL_DATAADAPTER){
            if ($clean){
                $sql=new MySQLDataController();
                $sql->drop_all_tables();
            }
        }
    }
    public function getDbConstantFile(){
        return igk_sys_db_constant_cache();
    }
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
    public function getInfoDataEntry($name, $cardinality=0, $type=1, $expression="(.)+"){
        $utypeinfo=$this->getParam("m_userTypeInfo", array());
        return isset($utypeinfo[$name]) ? $this->m_userTypeInfo[$name]: array(
            IGK_FD_NAME=>$name,
            "clCardinality"=>$cardinality,
            "clType"=>$type,
            "clDataType"=>$expression
        );
    }
      private function setUserTypeInfo($t){
        $this->setParam("usertypeinfo", $t);
    }
    /**
     * 
     * @return mixed 
     */
    public function & getuserTypeInfo(){
        $r=$this->getParam("usertypeinfo");
        return $r;
    }
    public function getName(){
        return IGK_SYSDB_CTRL;
    }
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
    protected function initComplete($context=null){
        parent::initComplete();
        $this->RegValueTypeArray("USERTOKENID", null, 1, 1);
    }
    public static function Initialized($cl){
        return false;
    }
    public function IsFunctionExposed($func){
        return igk_is_conf_connected();
    }
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
  
}