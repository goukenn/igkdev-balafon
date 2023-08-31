<?php
// @author: C.A.D. BONDJE DOUE
// @file: RunTacCommand.php
// @date: 20230112 14:51:24
namespace IGK\System\Console\Commands;

use IGK\Helper\SysUtils;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\BalafonApplication;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Commands\ServerCommandHelper;
use IGK\System\Console\Logger;
use IGK\System\Console\TerminalActionCommand;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Command
*/
class RunTacCommand extends AppExecCommand{

    var $command = "--run:tac";

    var $category = "sys";

    var $desc = "terminal action command";

    public function exec($command) {
         // terminal action command
         Logger::print('start : ' . $this->command);
         Logger::success("terminal action command \n");
         DbCommandHelper::Init($command);
         ServerCommandHelper::Init($command);
         ($ctrl = igk_getv($command->options, '--controller')) &&
         (($ctrl = SysUtils::GetControllerByName($ctrl)) || igk_die("controller not found"));

         if ($ctrl)
            $ctrl::register_autoload();

         if ($user = igk_getv($command->options, "--user")){
            if ($ctrl) { 
               if (is_numeric($user)){
                  self::BindUser($ctrl, intval($user));
               }else {
                  $user = igk_get_user_bylogin($user) ?? igk_die('user not found');
                  self::BindUser($ctrl, $user->clId);
               }
            } else {
               Logger::warn("missing a controller...");
            }
         }
         if ($ctrl){
            $user_model = ($user = $ctrl->getUser()) ? $user->model() :null;
            BalafonApplication::BindCommandController($ctrl, $user_model);
         }
         $c = new TerminalActionCommand;
         return $c->run();

    }

}