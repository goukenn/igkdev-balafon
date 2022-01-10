<?php
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGKNumber;
use ZipArchive;
use IGK\Resources\R;
use IGK\System\Console\App;
use IGKControllerManagerObject;

// require_once (IGK_LIB_DIR."/Lib/Classes/Resources/R.php");


class ModuleList2Command extends AppExecCommand{

    var $command = "--module:list";
    var $category = "module";
    var $desc = "List installed modules";

    public function exec($command) {
        return (new ModuleListCommand())->exec($command, "ls");
    }

}