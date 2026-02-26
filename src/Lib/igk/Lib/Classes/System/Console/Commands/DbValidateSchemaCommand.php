<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbValidateSchemaCommand.php
// @date: 20220803 13:48:57
// @desc: 
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

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command = "--db:validate-schema";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc = "validate file schema";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = "db";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options = [ 
    ];

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|mixed $file
    */
    public function exec($command,  $file=null)
    {
        if (empty($file) || !igk_io_file_exists($file)){
            Logger::danger("exec command : file not found");
            return -1;
        }
        if (!igk_io_file_exists($db_schema = IGK_LIB_DIR."/Data/Schemas/db-schemas.xsd")){
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