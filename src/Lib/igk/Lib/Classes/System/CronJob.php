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
use Throwable;

/**
 * 
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

    public function execute()
    {
        if ($rows = Crons::select_all([
            Crons::FD_PROCESS=>0
        ])){
            self::ExecuteCronList($rows, function(){
                igk_ilog('file ... ');
                $s = new CronScriptHandler;
                return call_user_func_array([$s, 'handle'], func_get_args());
            });
            igk_ilog('running.....'. count($rows));
        }
        // try {
        //     igk_ilog("run cron - " . date("Ymd H:i:s"));
        //     Logger::info("#run:cron");
        //     $ctrl = $this->ctrl ?? SysUtils::GetControllerByName($this->ctrl, false);
        //     if ($this->provider) {
        //         if ($provider = CronJobProcess::GetJobProcessProvider($this->provider)) {
        //             if ($provider->exec("sys:invoke", null, $ctrl)) {
        //                 Logger::success(__("success : {0} ", $provider));
        //                 return 0;
        //             } else {
        //                 Logger::danger(__("crons failed"));
        //                 return -1;
        //             }
        //         } else {
        //             Logger::danger("provider not found.");
        //             return -1;
        //         }
        //     }


        //     $condition = ["!crons_process" => 1];
        //     if ($ctrl &&  ($ctrl = igk_getctrl($ctrl, false))) {
        //         $condition["crons_class"] = get_class($ctrl);
        //     }
        //     $rows = Crons::select_all($condition);

        //     foreach ($rows as $r) {

        //         if ($provider = CronJobProcess::GetJobProcessProvider($r->crons_script)) {
        //             if ($provider->exec($r->crons_name, json_decode($r->crons_options), $ctrl)) {
        //                 $r->crons_process = 1;
        //                 Logger::success("success :" . $r->crons_name);
        //             } else {
        //                 $r->crons_process = 2;
        //                 Logger::danger(__("crons failed : {0}", $r->crons_name));
        //             }
        //             $r->update();
        //         } else {
        //             Logger::info("provider not found for : " . $r->crons_script);
        //             if (file_exists($file = IGK_LIB_DIR . "/Crons/" . $r->crons_script)) {
        //                 // 
        //                 Logger::info("execute: " . igk_io_collapse_path($file));
        //                 $options = json_decode($r->crons_options);
        //                 if (CronJobProcess::RunFile($file, ["options" => (object)$options])) {
        //                     Logger::success("complete");
        //                     $r->crons_process = 1;
        //                 } else {
        //                     $r->crons_process = 2;
        //                     Logger::danger("failed");
        //                 }
        //                 $r->update();
        //             }
        //         }
        //     }
        // } catch (Throwable $ex) {
        //     Logger::danger(":" . $ex->getMessage());
        //     return false;
        // }
        // Logger::success("DONE");
        return 0;
    }
    /**
     * 
     * @param array $crons 
     * @param callable $handle_cron_script 
     * @return void 
     * @throws Exception 
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
                            //$start = $start[0] + ($start[1] /1000);
                            unset($d['(@error)']);
                            if (isset($d['@params'])) {
                                $arg = $d['@params'];
                            }
                            try {
                                $start = igk_start_time('cron_exec');
                                $status = call_user_func_array([$cl, $fc], [(object)[
                                    'args' => $arg,
                                    'last-execution' => igk_getv($d, '@last-execution')
                                ]]);
                                $l = igk_execute_time('cron_exec', $start);

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
