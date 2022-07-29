<?php
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use SQLQueryUtils;

class CSSCommand extends AppExecCommand{
    var $command = "--css:dist";
    var $desc = "get core balafon css"; 
    var $category = "css";
  
    public function help(){
        parent::help();         
      
    }
    public function exec($command)
    {    
        Logger::print(igk_css_doc_get_def(igk_app()->getDoc()));
    }   
}