<?php
// @author: C.A.D. BONDJE DOUE
// @filename: OutSideLinksCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands;
use IGK\System\Console\App;
use IGK\System\Console\AppCommand;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use ControllerInitListener;
use IGK\Helper\IO as IGKIO;
use \ApplicationController;

use Illuminate\Support\Facades\Log;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class OutSideLinksCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command = "--outsidelinks";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = "utility";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc  = "retrieve all outside links";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options = [ 
    ];

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|mixed $path
    * @param mixed $viewname
    */

    public function exec($command, $path=null, $viewname=""){
        if (empty($path)){
            $path = getcwd();
        } 
        $links = [];
        $file = $path;
        $tsourcedir = [$path];
        while($sourcedir = array_pop($tsourcedir)){
            if ($hdir = opendir($sourcedir)){
                while($c = readdir($hdir)){
                    if (($c==".") || ($c=="..")){
                        continue;
                    }
                    $mdir = $sourcedir.DIRECTORY_SEPARATOR.$c;             
                    if (is_link($mdir) && empty(strstr($rp= realpath($mdir), $file)) && is_dir($rp)){
                        $links[] = $mdir;  
                        continue;                          
                    }
                    if (is_dir($mdir)){
                        array_push($tsourcedir, $mdir);
                    }
                }
                closedir($hdir);
            }  
        }        
        Logger::print(implode("\n", $links));        
        Logger::success("done\n");
    }

    /**
    * auto generate doc.
    */

    public function help(){
        parent::help();
        Logger::print("-");
        Logger::info("Retrieve project outsie links");
    }
}