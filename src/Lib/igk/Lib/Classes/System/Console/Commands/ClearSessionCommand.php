<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ClearSessionCommand.php
// @date: 20220803 13:48:57
// @desc: 


use IGK\Controllers\SessionController;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;

class ClearSessionCommand extends AppExecCommand{
    var $command = "--clearsession";    
    var $desc = "clear session command";
    
    /**
     * exec the command
     */
    public function exec($command)
    { 
        //defined("NO")
        /**
         * @var SessionController $sess
         */
        if ($sess = igk_getctrl(IGK_SESSION_CTRL, false)){
            Logger::info("Clearing session");
            $tab=igk_sys_get_all_openedsessionid(false);           
            // $cid=session_id();
            @session_write_close();
            $c=0;
            foreach($tab as $k=>$v){
                Logger::info("remove ". $v["file"]);
                @unlink($v["file"]);
                $c++;
            }
            $sess->clearAllSession();
        }      
 
        Logger::success("done");
    }
    
}
 