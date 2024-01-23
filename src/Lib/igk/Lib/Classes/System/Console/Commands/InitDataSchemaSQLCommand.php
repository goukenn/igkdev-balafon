<?php
// @author: C.A.D. BONDJE DOUE
// @filename: InitDataSchemaSQLCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Database\DbSchemas;
use IGK\Helper\Utility;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\Helper\DbUtility;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\XML\XmlNode;
use IGKEvents;
use IGKException;
use IGKNonVisibleControllerBase;
use IGKSysUtil;
use ReflectionException;

/**
 * initialize data schema
 * @package IGK\System\Console\Commands
 */
class InitDataSchemaSQLCommand extends AppExecCommand{
    var $command = "--db:schema";
    var $desc = "get controller db schema"; 
    var $category = "db";

    var $options = [
        "controller*"=>"controller to target",
        "file*"=>"schema file to export",
        "-o:[xml|json]"=>"export type xml|json"
    ];
    var $usage = '[controller] [file] [options]';

    
    /**
     * 
     * @param mixed $command 
     * @param mixed $ctrl 
     * @param mixed $file 
     * @return int 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    public function exec($command,  $ctrl=null, $file=null)
    {    
        require_once(__DIR__."/.InitDataSchemaController.pinc");
        $v_check =false;
        if (!$ctrl  || (($v_check=true) && !($ctrl = igk_getctrl($ctrl, false)))){
            $v_check && Logger::warn('missing controller');
            $ctrl = new InitDataSchemaController();
        }
        if ($file===null){ 
            $file = $ctrl::getDataSchemaFile();
        }
        if (!$file || !file_exists($file)){
            Logger::danger("data schema file not found");
            return -1;
        }
        $options = igk_getv($command->options, "-o", 'xml');
        $resolvname = $options != "json";       
        $schema = igk_db_load_data_schemas($file, $ctrl, $resolvname);
        if (!$schema){
            Logger::danger("schema not valid");
            return -2;
        }
        igk_set_env(IGK_ENV_DB_INIT_CTRL, $ctrl); 
        $tables = igk_getv($schema, "tables"); 
        switch($options)
        {
            case 'json':
                echo Utility::To_JSON($tables, [
                    "ignore_empty"=>1,
                ], JSON_PRETTY_PRINT);
                igk_exit();
            case 'xml':
                $n = DbUtility::ExportToXMLSchemaData($ctrl, $tables); 
                if ($version = $schema->version){
                    $db = \IGK\System\Version::Parse($version);
                    $db->release++;
                    $n['version']= $db.'';
                }
                $n['author'] = $this->getAuthor($command);  
                $n->renderXML();
                break;
        } 
        // igk_hook(IGKEvents::HOOK_DB_INIT_ENTRIES, array($ctrl));
        // igk_hook(IGKEvents::HOOK_DB_INIT_COMPLETE, ["controller"=>$ctrl]);
        // Logger::success("Schema complete");
        return 0;
    }
    public function help(){
        parent::help();
        Logger::print("file [-o:[json|xml]]");
    }
   
}

