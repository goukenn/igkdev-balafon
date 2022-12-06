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

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class RequestUriViewCommand extends RequestViewCommand{
    var $command = '--request:uri';

    var $desc = 'request uri call'; 
 
    public function doRequest($command, $path){ 
        igk_wln_e($path);
        require_once IGK_LIB_DIR.'/igk_request_handle.php';
        igk_sys_handle_uri($path);
    }

}