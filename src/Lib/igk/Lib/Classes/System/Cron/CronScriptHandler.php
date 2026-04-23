<?php
// @author: C.A.D. BONDJE DOUE
// @file: CronScriptHandler.php
// @date: 20250416 10:51:02
namespace IGK\System\Cron;

/**
* auto generate doc.
* @package IGK\System\Cron
* @author C.A.D. BONDJE DOUE
*/
/**
 * cron script handler 
 * @package 
 */
class CronScriptHandler
{
    /**
    * Property: file.
    * @var mixed
    */
    var $file;
    /**
    * Property: args.
    * @var mixed
    */
    var $args;
    /**
    * Property: status.
    * @var mixed
    */
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