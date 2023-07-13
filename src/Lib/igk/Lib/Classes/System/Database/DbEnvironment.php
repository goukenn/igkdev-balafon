<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbEnvironment.php
// @date: 20230703 15:06:04
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
class DbEnvironment{
    static $sm_instance;

    var $no_db_select = false;

    private function __construct(){        
    }
    /**
     * get shared instance
     * @return mixed 
     */
    public static function getInstance(){
        $f = self::$sm_instance ?? self::$sm_instance = new self;
        igk_environment()->set("sys://Db", $f);
        return $f;
    }

}