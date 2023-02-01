<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssCommand.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use SQLQueryUtils;

/**
 * get core style definition
 * @package IGK\System\Console\Commands
 */
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