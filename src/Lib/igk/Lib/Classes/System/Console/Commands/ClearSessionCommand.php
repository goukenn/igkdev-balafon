<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ClearSessionCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands;
use IGK\Controllers\SessionController;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\CoreFileSystem;
use IGK\System\IO\File\PHPScriptBuilder;
class ClearSessionCommand extends AppExecCommand{
    var $command = "--clearsession";    
    var $desc = "clear session command";
    var $expired_duration;
    var $skip = false;
    /**
     * exec the command
     */
    public function exec($command)
    { 
        if ($command){
           $this->expired_duration = igk_getv($command->options, '--living');
        }
        //defined("NO")
        /**
         * @var SessionController $sess
         */
        if ($sess = igk_getctrl(IGK_SESSION_CTRL, false)){
            $tab=igk_sys_get_all_openedsessionid(false);           
            if(($c = count($tab)) == 0){ 
                $this->skip = true;
                return;
            }
            Logger::info("Clearing session ...".$c);
            // - |  $cid=session_id();
            @igk_sess_write_close();
            $c=0;
            // $fs = new CoreFileSystem;
            $fc = function(){return true;};
            if (!is_null($rs = $this->expired_duration)){
                $time = time();
                $fc = function($file)use($time, $rs){
                    $r = @filemtime($file);
                    $d = $time - $r;
                    return $d > $rs;
                };
            }
            foreach($tab as $k=>$v){
                $file = $v["file"];
                if ($fc($file)){
                    Logger::info("remove ". $v["file"]);
                    @unlink($v["file"]);
                    $c++;
                }
            }
        }   
        Logger::success("done");
    }
}