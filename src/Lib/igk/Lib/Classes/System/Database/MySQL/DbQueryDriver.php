<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbQueryDriver.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Database\MySQL; 

use IGK\Database\DbQueryDriver as DatabaseDbQueryDriver;
use IGKConstants;
use IGKException;

/**
 * mysql query driver 
 */
class DbQueryDriver extends DatabaseDbQueryDriver {

    public static function Create($options=null, & $error = null ){
        $o = parent::Create($options, $error); 
        return $o;
    }    
    public function getVersion(){
        if ($this->m_resource){
            return $this->m_resource->server_info;
        }
        return null;
    }
   
    /**
     * initilzie data driver 
     * @param mixed $resource 
     * @return bool 
     * @throws IGKException possibility of no default type zone
     */
    protected function initialize($resource): bool {
        $time_zone = igk_configs()->get('date_time_zone', IGKConstants::DEFAULT_TIME_ZONE);
        $t=igk_db_query("SELECT SUBSTRING_INDEX(CURRENT_USER(),'@',1)", $resource);
        if($t && (igk_db_num_rows($t) == 1)){
            if (!empty($time_zone)){
                // can throw exception if not allowed to set time zone - 
                igk_db_query("SET time_zone='".$time_zone."';", $resource);
            }
            return true;
        }
        return false;
    }

    ///<summary></summary>
    ///<param name="t"></param>
    ///<param name="msg" default=""></param>
    /**
     * 
     * @param mixed $t
     * @param mixed $msg the default value is ""
     */
    protected function dieinfo($t, $msg = "", $code = 0)
    {
        if (!$t) {
            $this->m_error = igk_mysql_db_error($this->m_resource);
            $d = igk_mysql_db_errorc($this->m_resource);
            $m = $em = $this->getError(); 
            if (!igk_is_cmd()) {
                $m = "<div><div class=\"igk-title-4 igk-danger\" >/!\\ " . __CLASS__ . " Error</div><div>" . $em . "</div>" . "<div>Code: " . $d . "</div>" . "<div>Message: <i>" . $msg . "</i></div></div>";
            } else {
                $m = implode(PHP_EOL, [
                    "SQL ERROR",
                    "code: $d",
                    "error: $m",
                    "query: " . $this->getLastQuery(),
                ]);
            }
            $this->ErrorString = $em;
            switch ($d) {
                case 1062:
                    // + | duplicate entry error Code 
                case 1146: 
                        // + | table not found
                        // make compatible with mysql 8.to raise error
                        // throw new IGKException("custom table not found: ".$m);
                    break;
            }       
            igk_push_env("sys://adapter/sqlerror", $m);
            if (igk_environment()->isDev()) {
                throw new \IGKException($m);
            }
        }
    }
}