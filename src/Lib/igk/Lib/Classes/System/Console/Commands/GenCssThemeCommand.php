<?php
// @author: C.A.D. BONDJE DOUE
// @file: GenCssThemeCommand.php
// @date: 20221008 14:42:37
namespace IGK\System\Console\Commands;

use IGK\Controllers\SysDbController;
use IGK\Css\CssThemeOptions; 
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Html\Css\CssUtils;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class GenCssThemeCommand extends AppExecCommand{

    var $command = '--project:css-dist';
    var $desc = "get controller's css distribution";
    var $usage = "";
    var $category = 'css';

    var $options = [
        '--theme:(name)'=>'set preferered theme\'s name. dark|light|both default is both.',
        '--prefix:(prefix)'=>'set prefix to use for render',
    ];
    public function showUsage(){
        parent::showUsage();
        Logger::warn($this->command ." controller [options]");
    }
    public function exec($command, $controller=null) {  
        $controller = $controller ?? SysDbController::ctrl();      
        is_null($controller) && igk_die("controller required");
        if (!$ctrl  = igk_getctrl($controller, false)){
            Logger::danger("controller not found");
            return -1;
        }
        $theme = igk_getv($command->options, '--theme', 'both');
        $embed = property_exists($command->options, '--embed');
        $prefix = igk_getv($command->options, '--prefix', '');

        is_array($theme) && igk_die('invalid arg theme ');

        $src = CssUtils::GenCss($ctrl, $theme, $embed, $prefix );
        echo $src.PHP_EOL;        
    }

}