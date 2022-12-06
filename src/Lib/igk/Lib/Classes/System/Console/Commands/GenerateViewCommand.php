<?php
// @author: C.A.D. BONDJE DOUE
// @file: GenerateViewCommand.php
// @date: 20221129 10:48:32
namespace IGK\System\Console\Commands;

use IGK\DocumentParser\DocumentParser;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Command
*/
class GenerateViewCommand extends AppExecCommand{
    var $command = "--gen:view";
    var $options = [
        "--shared"=>"flag: share resources"
    ];
    var $cat = "tools";

    public function showUsage(){
        Logger::print(sprintf("%s controller domain [view]", $this->command));
    }
    public function exec($command, $controller = null, $domain = null, $view=null) { 
        $controller = self::GetController($controller, false) ?? igk_die("require controller");
        $view = $view ?? 'default';
        if (is_null($domain)){
            igk_die("domain require");
        }
        $g = new DocumentParser;
        $g->domain = $domain; 
        $g->controller = $controller;  
        $g->standalone = property_exists('--shared', $command->options) ? false : true;

        $content = igk_curl_post_uri($domain);
        if ($content && (igk_curl_status() == 200)){
            igk_io_w2file("/tmp/out.html", $content);
            if ($g->parse($content)){
                $g->buildView($view);
                Logger::success("done");
            }
        } else {
            Logger::danger("no content found from [".$domain."]");
        }
    }

}