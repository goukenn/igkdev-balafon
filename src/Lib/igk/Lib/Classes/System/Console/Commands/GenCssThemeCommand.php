<?php
// @author: C.A.D. BONDJE DOUE
// @file: GenCssThemeCommand.php
// @date: 20221008 14:42:37
namespace IGK\System\Console\Commands;

use igk\devtools\DocumentParser\UriDetector;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Html\Css\CssUtils;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class GenCssThemeCommand extends AppExecCommand{

    var $command = "--gen:css";
    var $desc = "generate controller's css content command";
    var $usage = "";

    public function showUsage(){
        parent::showUsage();
        Logger::warn($this->command ." controller [options]");
    }
    public function exec($command, $controller=null) { 
        // --gen:css testController --theme_name:dark 
        is_null($controller) && igk_die("controller required");
        if (!$ctrl  = igk_getctrl($controller, false)){
            Logger::danger("controller not found");
            return -1;
        }
        $src = CssUtils::GenCss($ctrl);
        echo $src.PHP_EOL;        
    }

}