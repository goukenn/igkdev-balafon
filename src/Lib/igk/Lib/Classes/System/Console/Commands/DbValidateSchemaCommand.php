<?php

namespace IGK\System\Console\Commands;

use IGK\Controllers\BaseController;
use IGK\Helper\Utility;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Helper;
use IGK\XML\XSDValidator;
use IGKEvents;
use IGKNonVisibleControllerBase;



/**
 * initialize data schema
 * @package IGK\System\Console\Commands
 */
class DbValidateSchemaCommand extends AppExecCommand{
    var $command = "--db:validate-schema";
    var $desc = "validate file schema"; 
    var $category = "db";

    var $options = [ 
    ];
 

    public function exec($command,  $file=null)
    {
        if (!file_exists($file)){
            Logger::danger("file not found");
            return -1;
        }
        if (!file_exists($db_schema = IGK_LIB_DIR."/Data/Schemas/db-schemas.xsd")){
            Logger::danger("schema validatin is missing.");
            return -2;
        }
        $error = [];
        $c = XSDValidator::ValidateSource(file_get_contents($file), file_get_contents($db_schema), $error);

        if (!$c && $error && count($error)>0){
            print_r($error);
            Logger::danger("Not a valid --db schema");
            return -3;
        }
        Logger::success("File is OK");
    }
}

