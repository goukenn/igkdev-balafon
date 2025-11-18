<?php
// @author: C.A.D. BONDJE DOUE
// @file: CronScriptHandler.php
// @date: 20250416 10:51:02
namespace IGK\System\Cron;
/**
* 
* @package IGK\System\Cron
* @author C.A.D. BONDJE DOUE
*/
/**
 * cron script handler 
 * @package 
 */
class CronScriptHandler
{
    var $file;
    var $args;
    var $status;
    /**
     * argument to handler 
     * @return int status code  
     */
    public function handle()
    {
        extract($this->args = func_get_arg(1));
        return include $this->file = func_get_arg(0);
    }
}