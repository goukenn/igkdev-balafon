<?php
// @author: C.A.D. BONDJE DOUE
// @filename: JSCommand.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use SQLQueryUtils;

class JSCommand extends AppExecCommand{
    var $command = "--js:dist";
    var $desc = "get core minified js"; 
    var $category = "js";
  
    var $options = [
        '--js-debug'=>'flag: js debug on generation'
    ];
    public function exec($command)
    {   
        Logger::print(igk_sys_balafon_js(null, property_exists($command->options, '--js-debug')));
    }   
}