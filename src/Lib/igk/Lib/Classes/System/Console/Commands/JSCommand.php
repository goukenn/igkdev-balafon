<?php
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use SQLQueryUtils;

class JSCommand extends AppExecCommand{
    var $command = "--js:dist";
    var $desc = "get core minified js"; 
    var $category = "js";
  
    public function help(){
        parent::help();         
      
    }
    public function exec($command)
    {   
        Logger::print(igk_sys_balafon_js(igk_app()->getDoc()));
    }   
}