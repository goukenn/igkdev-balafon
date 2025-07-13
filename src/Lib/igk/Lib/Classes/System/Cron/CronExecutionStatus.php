<?php
// @author: C.A.D. BONDJE DOUE
// @file: CronExecutionStatus.php
// @date: 20250415 14:38:17
namespace IGK\System\Cron;
/**
* 
* @package IGK\System\Cron
* @author C.A.D. BONDJE DOUE
*/
abstract class CronExecutionStatus{
    const RESTART = 0;
    const STOP = 1;
    const SKIP = -1;
}