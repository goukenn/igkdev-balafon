<?php
// @author: C.A.D. BONDJE DOUE
// @file: CommandHelper.php
// @date: 20250415 11:39:43
namespace IGK\System\Cron;

use Cron\CronExpression;
use IGK\System\Console\Commands\ClearSessionCommand;
use IGK\Constants;

/**
* 
* @package IGK\System\Cron
* @author C.A.D. BONDJE DOUE
*/
class CommandHelper{
    
    public static function CleanSession(): ?int{       
        if (class_exists(ClearSessionCommand::class)){
            $e = func_get_arg(0);

            $cmd = new ClearSessionCommand;
            $cmd->expired_duration = igk_getv($e->args, 'duration') ??  
                igk_configs()->get('session_living', Constants::SESS_LIVING_TIME);
            $cmd->exec(null);
            if ($cmd->skip){
                return CronExecutionStatus::SKIP;
            }
            return CronExecutionStatus::RESTART;
        }
        return CronExecutionStatus::STOP;
    }
}