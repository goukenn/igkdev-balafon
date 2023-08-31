<?php
// @author: C.A.D. BONDJE DOUE
// @file: GenCssThemeCommand.php
// @date: 20221008 14:42:37
namespace IGK\System\Console\Commands;

use IGK\Controllers\SysDbController;
use IGK\Css\CssThemeOptions;
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

    var $options = [
        '--theme:[]'=>'set preferered theme\'s name. dark|light|both'
    ];
    public function showUsage(){
        parent::showUsage();
        Logger::warn($this->command ." controller [options]");
    }
    public function exec($command, $controller=null) { 
        // --gen:css testController --theme_name:dark   
        $controller = $controller ?? SysDbController::ctrl();      
        is_null($controller) && igk_die("controller required");
        if (!$ctrl  = igk_getctrl($controller, false)){
            Logger::danger("controller not found");
            return -1;
        }
        $theme = igk_getv($command->options, '--theme',CssThemeOptions::DEFAULT_THEME_NAME );
        $src = CssUtils::GenCss($ctrl, $theme);
        echo $src.PHP_EOL;        
    }

}