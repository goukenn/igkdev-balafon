<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssDistCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands\Css;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger; 
/**
 * get core style definition
 * @package IGK\System\Console\Commands
 */
class CssDistCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--css:dist";

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "get core balafon css";

    /**
    * Property: category.
    * @var mixed
    */
    var $category = "css";

    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = '[options]';

    /**
    * Property: options.
    * @var mixed
    */
    var $options = [
        "--min-file"=>"flag: min file",
        "--theme-export"=>"flag: theme export",
    ];

    /**
    * auto generate doc.
    */

    public function exec($command){   
        $minfile = property_exists($command->options, '--min-file');
        $theme_export = property_exists($command->options, '--theme-export');
        $src = igk_css_doc_get_def(igk_app()->getDoc(), $minfile, $theme_export);
        Logger::print($src);
    }   
}