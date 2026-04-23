<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CronJob.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\System;
use Exception;
use IGK\Helper\SysUtils;
use IGK\Helper\Utility;
use IGK\Models\Crons;
use IGK\System\Console\Logger;
use IGK\System\Cron\CronExecutionStatus;
use IGK\System\Cron\CronScriptHandler;
use IGK\System\Process\CronJobProcess;
use IGKEvents;
use Throwable;

/**
 * 
 * @package IGK\System
 */
/**
* auto generate doc.
* @package IGK\System
*/
class CronJob
{
    /**
     * provider
     * @var mixed
     */
    var $provider;
    /**
     * controller
     * @var ?string
     */
    var $ctrl;
    /**
    * Executes.
    */
    public function execute()
    {
        igk_ilog('file ... select cron definition');
        if ($rows = Crons::select_all([
            Crons::FD_PROCESS=>0
        ])){
            self::ExecuteCronList($rows, function(){
                igk_ilog('file ... ');
                $s = new CronScriptHandler;
                return call_user_func_array([$s, 'handle'], func_get_args());
            });
            igk_ilog('running...cronjob.tables rows #'. count($rows));
        } 
        igk_hook(IGKEvents::HOOK_CRONJOB, ['task'=>'cronjob', 'date'=> date("Ymd H:i:s")]);      
        return 0;
    }
    /**
    * auto generate doc.
    * @param callable $handle_cron_script
    * @return void
    */
    public static function ExecuteCronList(array $crons, callable $handle_cron_script, ?string $exclude_fs = null)
    {
        if (!class_exists('CommandHelper', false))
            class_alias(\IGK\System\Cron\CommandHelper::class, 'CommandHelper');
        $dir = IGK_LIB_DIR.'/Crons';
        $exclude_fs = $exclude_fs ?? IGK_LIB_DIR.'/cron-tab.php';
        $json_db_flag = JSON_UNESCAPED_SLASHES;
        if ($crons) {
            while (count($crons) > 0) {
                $row = array_shift($crons);
                if ($row->process == 0) {
                    $status = -1;
                    $scr = $row->script;
                    if (preg_match('/^\\w+@\\w+$/', $scr)) {
                        list($cl, $fc) = igk_extract(explode('@', $scr, 2), '0|1');
                        if (class_exists($cl) && method_exists($cl, $fc)) {
                            $arg = $d = json_decode($row->options ?? '[]', true) ?? [];
                            unset($d['(@error)']);
                            if (isset($d['@params'])) {
                                $arg = $d['@params'];
                            }
                            try {
                                igk_start_time('cron_exec');
                                $status = call_user_func_array([$cl, $fc], [(object)[
                                    'args' => $arg,
                                    'last-execution' => igk_getv($d, '@last-execution')
                                ]]);
                                $l = igk_execute_time('cron_exec');
                                if ($status == CronExecutionStatus::SKIP) {
                                    continue;
                                }
                                $response = ['@params' => $arg, 'duration' => $l . 's'];
                                if ($status == CronExecutionStatus::RESTART) {
                                    $response['@last-execution'] = igk_date_now();
                                }
                                $row->options = json_encode($response);
                                $row->process = $status;
                            } catch (\Exception $ex) {
                                $d['(@error)'] = $ex->getMessage();
                                $row->options = json_encode($d);
                            }
                        }
                    } else {
                        igk_ilog('try load script on : '.$dir.'/'.$scr);
                        if (file_exists($fs =  $dir . '/' . $scr) && ($exclude_fs != $fs)) {
                            try {
                                $arg = $d =  json_decode($row->options ?? '[]', true) ?? [];
                                if (key_exists('@params', $d)) {
                                    $d = $d['@params'];
                                }
                                igk_start_time($key_time = 'cron_file');
                                $g = $handle_cron_script($fs, $d);
                                $time = igk_execute_time($key_time);
                                if ($g == CronExecutionStatus::SKIP) {
                                    continue;
                                }
                                $row->process = $g;
                                $row->options = json_encode([
                                    '@params' => $d,
                                    '@at' => date('Ymd His'),
                                    'duration' => $time . 's'
                                ], $json_db_flag);
                                $status = 0;
                            } catch (\IGKException $ex) {
                                $d['(@error)'] = $ex->getMessage();
                                $row->options = json_encode($d);
                            }
                        } else {
                            continue;
                        }
                    }
                    $row->status = $status;
                    $row->update_at = null;
                    $row->save();
                }
            }
        }
    }
}