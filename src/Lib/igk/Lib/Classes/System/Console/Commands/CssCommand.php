<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssCommand.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use SQLQueryUtils;

/**
 * get core style definition
 * @package IGK\System\Console\Commands
 */
class CSSCommand extends AppExecCommand{
    var $command = "--css:dist";
    var $desc = "get core balafon css"; 
    var $category = "css";
    var $usage = '[options]';
    var $options = [
        "--min-file"=>"flag: min file",
        "--theme-export"=>"flag: theme export",
    ];
    /**
     * 
     */
    public function exec($command){   
        $minfile = property_exists($command->options, '--min-file');
        $theme_export = property_exists($command->options, '--theme-export');
        $src = igk_css_doc_get_def(igk_app()->getDoc(), $minfile, $theme_export);
        Logger::print($src);
    }   
}