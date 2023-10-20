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
    var $group = "management";
    var $command = "--auth";
    var $desc = 'Manage autor';

    const AVAILABLE_ACTION = "auths|groups|grant";
    var $usage = 'controller --action:'.self::AVAILABLE_ACTION;
    public function help()
    {
        Logger::success($this->command . " [controller] [--action:options*]");
        Logger::print("");
        Logger::print($this->desc);
        Logger::print("");
        Logger::info("options*:");
        foreach (explode("|", self::AVAILABLE_ACTION) as $k) {
            Logger::print("\t{$k}");
        }
    }

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
            case "groups": // view groups
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
                // igk_wln_e($g->getRows());
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
                // Logger::print("Grant : ".$auth ." to ". $group);
                // $g = Groups::select_row(["clName"=>$group]);
                // $auths = Authorizations::select_row(["clName"=>$auth]);
                // Logger::print($g->to_json());
                // if (!$g && ! ($g = Groups::insertIfNotExists(["clName"=>$group]))){
                //     igk_die("failed to add group");
                // }
                // if (!$auths && !($auths = Authorizations::insertIfNotExists(["clName"=>$auth]))){
                //     igk_die("failed to add auth");
                // }
                // $id = Groupauthorizations::insertIfNotExists(
                //     ["clGroup_Id"=>$g->clId, "clAuth_Id"=>$auths->clId],
                //     ["extra"=>["clGrant"=>1]]);

                // igk_wln_e("the id :::: ", $id);
                break;
            case "help":
            default:
                break;
        }

        // igk_wln_e("loging : ", $g, $g->groups());//, $g::getMacroKeys());
        Logger::print("Done");
    }
}
