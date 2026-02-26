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

    /**
    * auto generate doc.
    */
    public function getCanInitDb(){
        return true;
    }

    /**
    * auto generate doc.
    * @param mixed $navigate
    * @param mixed $force
    * @param mixed $clean
    */
    protected static function dropDb($navigate=1, $force=0, $clean=false){
        $c=igk_getctrl(__CLASS__, false);
        if($c->getDataAdapterName() == IGK_MYSQL_DATAADAPTER){
            if ($clean){
                $sql=new MySQLDataController();
                $sql->drop_all_tables();
            }
        }
    }

    /**
    * auto generate doc.
    */
    public function getDbConstantFile(){
        return igk_sys_db_constant_cache();
    }

    /**
    * auto generate doc.
    */
    protected function getEntryNamespace(){
        return rtrim(\IGK::class, "\\");
    }

    /**
    * auto generate doc.
    */
    public function getClassesDir(){
        return IGK_LIB_CLASSES_DIR;
    }

    /**
    * auto generate doc.
    */
    public function getArticlesDir()
    { 
        return IGK_LIB_DIR."/".IGK_ARTICLES_FOLDER;
    }

    /**
    * auto generate doc.
    * @param mixed $name
    * @param mixed $cardinality
    * @param mixed $type
    * @param mixed $expression
    */
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

    /**
    * auto generate doc.
    * @return string
    */
    public function getName(): string{
        return IGK_SYSDB_CTRL;
    }
    /**
     * get use of data schema
     * @return true 
     */

    public function getUseDataSchema():bool{
        return true;
    }

    /**
    * auto generate doc.
    */
    public function getDataDir(){
        return IGK_LIB_DIR."/".IGK_DATA_FOLDER;
    }

    /**
    * auto generate doc.
    * @param null|mixed $context
    */
    protected function initComplete($context=null){
        parent::initComplete();
        $this->RegValueTypeArray("USERTOKENID", null, 1, 1);
    }

    /**
    * auto generate doc.
    * @param mixed $cl
    */
    public static function Initialized($cl){
        return false;
    }

    /**
    * auto generate doc.
    * @param mixed $func
    */
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