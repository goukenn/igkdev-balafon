<?php
// @author: C.A.D. BONDJE DOUE
// @file: CronExecutionStatus.php
// @date: 20250415 14:38:17
namespace IGK\System\Cron;

/**
* auto generate doc.
* @package IGK\System\Cron
* @author C.A.D. BONDJE DOUE
*/
abstract class CronExecutionStatus{
    /**
    * Constant: restart.
    * @var mixed
    */
    const RESTART = 0;
    /**
    * Constant: stop.
    * @var mixed
    */
    const STOP = 1;
    /**
    * Constant: skip.
    * @var mixed
    */
    const SKIP = -1;
}