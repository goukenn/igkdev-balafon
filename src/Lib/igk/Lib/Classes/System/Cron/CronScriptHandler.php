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

    /**
    * auto generate doc.
    * @var mixed
    */
    var $file;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $args;

    /**
    * auto generate doc.
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