<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbUpdateControllerSchemaCommand.php
// @date: 20230118 12:38:15
namespace IGK\System\Console\Commands;
use IGK\Controllers\SysDbController;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\Helper\DbUtility;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class DbUpdateControllerSchemaCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = '--db:schema-update';
    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'db';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'udpate data-schema and increment the release version';
    /**
    * Shows Usage.
    */
    public function showUsage(){
        parent::showUsage();
        Logger::print(sprintf("%s controller [file]", $this->command));
    }
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $controller
    * @param null|string $file
    */
    public function exec($command, ?string $controller=null, ?string $file = null ) {
        if ($controller && !($ctrl = self::GetController($controller, false))){            
            igk_die("controller not found");
        }else{
            $ctrl = $ctrl ?? SysDbController::ctrl();
        }        
        DbUtility::UpdateDbSchema($ctrl,[
            'author'=>$this->getAuthor($command),
            'outputfile'=>$file
        ]);
        Logger::success("update - done");
    }
}