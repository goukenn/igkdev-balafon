<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CronJobProcess.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Process;

use IGK\Controllers\BaseController;
use IGK\Models\Crons;

class CronJobProcess{
    const mail_script = "process.mail";
    /**
     * create a new cronjob identifier
     * @return string 
     */
    public static function NewCronJobIdentifier(){
        return igk_create_guid();
    }
    public static function Register($name, $script, $options, ?BaseController $ctrl=null){
        if ($options && ($provider = self::GetJobProcessProvider($script))){
            $options = $provider->treat($options);
        }

        return Crons::create([
            "crons_name"=>$name,
            "crons_script"=>$script,
            "crons_class"=>$ctrl ? get_class($ctrl):null,
            "crons_options"=>json_encode($options, JSON_UNESCAPED_SLASHES)
        ]) !==null;
    }
    /**
     * 
     * @param mixed $script_file 
     * @return CronJobProcessMailProvider 
     */
    public static function GetJobProcessProvider($script_file){
        $tab = & igk_environment()->createArray("sys://cronProccess");
        if ($cl = igk_getv($tab, $script_file)){
            return $cl;
        }
        if ($script_file == self::mail_script){
            $cl =  new CronJobProcessMailProvider();
            $tab[$script_file] = $cl;
        }

        return $cl;
    }
    /**
     * run cron script 
     * @param  string $file script to run
     * @param  ?array $args argument to pass
     * @return bool 
     */
    public static function RunFile(){
        extract(func_get_arg(1));
        return include(func_get_arg(0));
    }
}