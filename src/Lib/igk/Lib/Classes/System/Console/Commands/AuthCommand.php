<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AuthCommand.php
// @date: 20220802 18:44:45
// @desc: auth command
namespace IGK\System\Console\Commands;
use IGK\Models\Authorizations;
use IGK\Models\Groupauthorizations;
use IGK\Models\Groups;
use IGK\Models\Usergroups;
use IGK\Models\Users;
use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use SQLQueryUtils;

/**
 * auth command helper
 * @package IGK\System\Console\Commands
 */
class AuthCommand extends AppExecCommand
{
    /**
    * Property: group.
    * @var mixed
    */
    var $group = "management";
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--auth";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'Manage auth';
    /**
    * Constant: available action.
    * @var mixed
    */
    const AVAILABLE_ACTION = "auths|groups|grant";
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'controller --action:'.self::AVAILABLE_ACTION;
    /**
    * Property: action helps.
    * @var mixed
    */
    var $action_helps = [];
    /**
    * Help.
    */
    public function help()
    { 
        Logger::success($this->command . " [controller] [--action:options*]");
        Logger::print("");
        Logger::print($this->desc);
        Logger::print("");
        Logger::info("options*:");
        $tab = explode("|", self::AVAILABLE_ACTION);
        sort($tab);
        foreach ($tab as $k) {
            Logger::print("\t{$k}");
            $help = igk_getv($this->action_helps, $k);
            if ($help){
                Logger::print(str_repeat("\t", 6).$help);
            }
        }
    }
    /**
    * Exec.
    * @param mixed $command
    * @param null|mixed $username
    * @param mixed ...$options
    */
    public function exec($command, $username = null, ...$options)
    {
        DbCommandHelper::Init($command);
        $action = igk_getv($command->options, "-action", "help");
        if (empty($action) || is_array($action)) {
            die("not valid");
        }
        $g = Users::select_row(["clLogin" => $username], [
            "Operand" => "Or"
        ]);
        if (!$g) {
            Logger::danger("User not found");
            return -1;
        }
        if (!in_array($action, explode("|", self::AVAILABLE_ACTION))){
            Logger::danger("not a valid action");
            return -1;
        }
        switch ($action) {
            case "groups": 
                Logger::info("member of : ");
                array_map(function ($a) {
                    Logger::print(":> " . App::Gets(App::AQUA, $a->clName));
                }, (!$g ? null : $g->groups()) ?? []);
                break;
            case "auths":
                Logger::info("auths : ");
                array_map(function ($a) {
                    Logger::print(":>" .  App::Gets(App::AQUA, $a->name));
                }, (!$g ?null: $g->auths()) ?? []);
                break;
                break;
            case "grant":
                $group = $options[0];
                $auth = $options[1];
                if ($g->grantAuthorization($group, $auth)){
                    Logger::success("complete");
                } else {
                    Logger::danger("error");
                }
                break;
            case "help":
            default:
                break;
        }
        Logger::print("auth - done");
    }
}