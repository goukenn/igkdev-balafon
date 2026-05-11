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

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class ClearSessionCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--clearsession";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "clear session command";
    /**
    * Property: expired duration.
    * @var mixed
    */
    var $expired_duration;
    /**
    * Property: skip.
    * @var mixed
    */
    var $skip = false;
    /**
    * exec the command
    * @param mixed $command
    */
    public function exec($command)
    { 
        if ($command){
           $this->expired_duration = igk_getv($command->options, '--living');
        }
        /**
        * auto generate doc.
        * @var SessionController $sess
        */
        if ($sess = igk_getctrl(IGK_SESSION_CTRL, false)){
            $tab=igk_sys_get_all_openedsessionid(false);           
            if(($c = count($tab)) == 0){ 
                $this->skip = true;
                return;
            }
            @igk_sess_write_close();
            $c=0;
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