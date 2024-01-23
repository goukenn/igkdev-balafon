<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbDumpCommand.php
// @date: 20220727 19:30:34
// @desc: dump controller database

namespace IGK\System\Console\Commands;

use IGK\Controllers\BaseController;
use IGK\Database\DbSchemas;
use IGK\Helper\Utility;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\DbSchemaDefinitionAttributes;
use IGK\System\Database\DbUtils;
use IGK\System\Helper;
use IGK\System\Html\HtmlReader;
use IGK\Test\IGKHtmlReaderTest;
use IGKEvents;
use IGKNonVisibleControllerBase;



/**
 * dump controller database
 * @package IGK\System\Console\Commands
 */
class DbDumpCommand extends AppExecCommand{
    var $command = "--db:dump";
    var $desc = "dump controller db from schema"; 
    var $category = "db";
    var $options = [
        "controller"=>"controller to target",
        "file"=>"file to export",
        "-o"=>"export type xml|json",
        '--inject'=>'flag: replace dump fields to schema',
    ];
    var $help = "--db:dump controller [output_file] [-o:xml|json]";

    var $usage = 'controller [outfile] [options]';

    private $_entries;

    public function exec($command,  $ctrl=null, $file=null)
    {    
         
        if (!$ctrl  || !($ctrl = igk_getctrl($ctrl))){
            Logger::danger("controller required");
            return -1;
        
        }
        $type = igk_getv($command->options, "-o", "json");
        $v_inject = property_exists($command->options, "--inject"); // , "json");

        $ctrl::register_autoload();

        $this->_entries = [];
        $gen = $this->getGenerator($type);

        $man  = \IGK\System\IO\Helper::GenerateModel($ctrl, function()use($gen){
            if ($gen === $this){
                $gen->_generate(...func_get_args());
            }
            else {
                $gen->generate(...func_get_args());
            }
        }
        );
        switch($type){
            case 'xml':
                $entries = igk_create_xmlnode(IGK_ENTRIES_TAGNAME);
                foreach($this->_entries as $table=>$data){
                     $rows = $entries->add(IGK_ROWS_TAGNAME);
                     $rows['For']= $table;
                    foreach($data as $row){
                        $rows->add(IGK_ROW_TAGNAME)->setAttributes((array)$row);
                    }
                }
                $entries->renderAJX((object)['Indent'=>true]);

              
                break;
            
            default:
                igk_wl(json_encode($this->_entries, JSON_PRETTY_PRINT).PHP_EOL); 
            break;
        }

        // + | --------------------------------------------------------------------
        // + | inject data entries to data schema
        // + |
        if ($v_inject){
            $file = $ctrl->getDataSchemaFile();
            if (file_exists($file)){
                $doc= HtmlReader::LoadFile($file);   
                $schema = igk_getv($doc->getElementsByTagName(IGK_SCHEMA_TAGNAME), 0);
                $v_entries = $schema->getElementsByTagName(DbSchemas::ENTRIES_TAG);
                if (!$v_entries){
                    $schema->add($entries);
                } else {
                    // $p = $v_entries[0]->getParentNode();
                    // $v_entries[0]->remove();
                    // $p->add($entries);
                    $v_entries[0]->replaceWith($entries);

                    // $entries[0]->replaceWith($entries);
                }
            } else {
                $schema = igk_create_xmlnode(IGK_SCHEMA_TAGNAME);
                $v_def = new DbSchemaDefinitionAttributes;
                $v_def->createAt = date('ymd H:i:s');
                $v_def->ControllerName = $ctrl->getName();
                $schema->setAttributes((array)$v_def);
                $schema->add($entries);
            }
            Logger::info('store schema: '.$file);
            igk_io_w2file($file, $schema->render((object)['Indent'=>true]));
        }

        // Logger::success("Schema complete");
        return 0;
    }
    public function getGenerator($type){ 
        return $this; 
    }
    public function _generate(BaseController $ctrl, $table, $info, & $manifest = []){

        /**
         * @var \IGK\System\Database\MySQL\DataAdapter $ad data adapter
         * @var \IGK\System\Database\MySQL\IGKMySQLQueryResult $g query result
         */
        $ad = $ctrl::getDataAdapter(); 
        $tb = $ctrl::resolveTableName($table);
        $v_tabinfo = $ctrl::getDataTableDefinition($table);
        $g = $ad->selectAll($table);
        $rest = []; 
        if ($g && $g->RowCount){
            $v_dumpfields = $v_tabinfo ? DbUtils::GetDumpFields($v_tabinfo->columnInfo) : null;
            if ($v_dumpfields){
                $v_dumpfields = array_fill_keys(array_keys($v_dumpfields),1); // all is required
            }
            foreach($g->getRows() as $r){
                $v_r = $r->to_array();
                if ($v_dumpfields){
                    $v_r = igk_array_filter($v_r, $v_dumpfields, false);
                }
                if ($v_r)
                $rest[] = $v_r; // r->to_array();
            }
        } 
        $this->_entries[$tb] = $rest;
    }
    public function help(){
        parent::help();
        Logger::print("file [-o:[json]]");
    }
}

