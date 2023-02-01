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
        // igk_wln_e(__FILE__.":".__LINE__,  $path);
        require_once IGK_LIB_DIR.'/igk_request_handle.php';
        igk_sys_handle_uri($path);
        Logger::info('done');
    }

}