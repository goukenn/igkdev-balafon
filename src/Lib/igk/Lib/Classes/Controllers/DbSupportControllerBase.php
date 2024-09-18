<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbSupportControllerBase.php
// @date: 20220725 11:28:15
// @desc: DbSupport Controller

namespace IGK\Controllers;

use IGK\Controllers\RootControllerBase;
use IGKDbModelUtility;

/**
 * 
 */
abstract class DbSupportControllerBase extends RootControllerBase{
    protected abstract function getDataTableName(): ?string;
    protected abstract function getUseDataSchema(): bool;
    /**
     * get the data table info
     * @return null 
     */
    protected function getDataTableInfo(){
        return null;
    }
    /**
     * create a db utility class
     * @return mixed 
     */
    protected function getDb(){
        static $db;
        if($db === null){
			if (method_exists($this , "_createDbUtility")){
				$db = $this->_createDbUtility();
			}
			if (!$db ){
				$db=new IGKDbModelUtility($this); 
			}
        }
        return $db;
    }

}