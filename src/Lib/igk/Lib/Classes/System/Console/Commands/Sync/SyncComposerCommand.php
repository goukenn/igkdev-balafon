<?php

namespace IGK\System\Console\Commands\Sync;

use IGK\Helper\FtpHelper;
use IGK\System\Console\Commands\Sync\SyncAppExecCommandBase;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\File\PHPScriptBuilderUtility;
use IGK\System\Regex\Replacement;
use IGKException;
use ReflectionException;

class SyncComposerCommand extends SyncAppExecCommandBase{
    var $command = "--sync:composer";

    var $desc = 'sync:ftp use global shared composer';
    
    /**
     * get merged scripts
     * @return string[] 
     */
    protected function getMergedScripts(){
        return [
            IGK_LIB_DIR."/Inc/core/installer-helper.pinc",
            IGK_LIB_DIR."/Inc/core/composer.pinc",   
        ];
    }  
 
    /**
     * execute command
     * @param mixed $command 
     * @param mixed $args 
     * @return never 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function exec($command, ...$args) { 
        $this->syncScriptCommand($command, "install-composer.php", ["args"=>$args]); 
    } 
}