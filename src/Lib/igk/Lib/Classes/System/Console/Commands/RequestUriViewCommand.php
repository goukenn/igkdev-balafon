<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RequestUriViewCommand.php
// @date: 20221124 00:58:06
// @desc: 

namespace IGK\System\Console\Commands;

use IGK\Helper\SysUtils;
use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Html\HtmlContext;
use IGK\System\Http\RequestHandler;
use IGK\System\Uri;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class RequestUriViewCommand extends RequestViewCommand{
    var $command = '--request:uri';

    var $desc = 'request uri call'; 
 
    /**
     * 
     * @param mixed $command 
     * @param mixed $path request path
     * @return void 
     * @throws IGKException 
     */
    public function doRequest($command, $path){ 
        igk_server()->SCRIPT_NAME = '/index.php';  
        RequestHandler::HandleRequestUri($path); 
        if ($ctrl = igk_getctrl(igk_configs()->default_controller, false)){            
            $g = new Uri($path);
            $path = $g->getPath();
            $_SERVER['REQUEST_URI'] = $g->getRequestUri();
            $_SERVER['QUERY_STRING'] = $g->getQuery(); 
            igk_server()->prepareServerInfo(); 
            $ctrl->setCurrentView($path);
        } 
        Logger::info('done');
    }

}