<?php
namespace IGK\System;

use IGK\Models\Crons;
use IGK\Models\DbLogs;
use IGK\System\Console\Logger;
use IGK\System\Process\CronJobProcess;
use Throwable;

class CronJob{
    var $provider; 
    var $ctrl;
    public function execute(){
        try {
            igk_ilog("run cron - " . date("Ymd H:i:s"));
            
            Logger::info("#run:cron");
         
            $ctrl = $this->ctrl;

            if ($this->provider){
                if ($provider = CronJobProcess::GetJobProcessProvider($this->provider)){
                    $ctrl &&  ($ctrl = igk_getctrl($ctrl, false));                                
                    if ($provider->exec("sys:invoke", null, $ctrl)){
                        Logger::success(__("success : {0} ", $provider));
                        return 0;
                    }else {
                        Logger::danger(__("crons failed"));
                        return -1;
                    }

                }else {
                    Logger::danger("provider not found.");
                    return -1;
                }
            }


            $condition = ["!crons_process" => 1];
            if ($ctrl &&  ($ctrl = igk_getctrl($ctrl, false))) {
                $condition["crons_class"] = get_class($ctrl);
            }
            $rows = Crons::select_all($condition); // 
            foreach ($rows as $r) {
                Logger::print($r->crons_script);
                if ($provider = CronJobProcess::GetJobProcessProvider($r->crons_script)) {
                    if ($provider->exec($r->crons_name, json_decode($r->crons_options), $ctrl)) {
                        $r->crons_process = 1;
                        Logger::success("success :" . $r->crons_name);
                    } else {
                        $r->crons_process = 2;
                        Logger::danger(__("crons failed : {0}", $r->crons_name));
                    }
                    $r->update();
                }
            }
        } catch (Throwable $ex) {
            Logger::danger(":" . $ex->getMessage());
            return false;
        }
        return 0;
    }
}