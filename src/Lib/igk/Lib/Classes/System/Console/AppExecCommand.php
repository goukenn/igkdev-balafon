<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AppExecCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace  IGK\System\Console;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\System\Console\Commands\DbCommandHelper;

use IGKException;

abstract class AppExecCommand extends AppCommand{
    protected $handle;
    /**
     * get option values
     * @param mixed $command 
     * @param string $list 
     * @return mixed 
     * @throws IGKException 
     */
    protected static function GetOptions($command, string $list){        
        foreach(explode("|", $list) as $m){
            if ($m = igk_getv($command->options, $m)){
                return $m;
            }
        }
    }
 
    /**
     * check if has options set in command
     * @param mixed $command 
     * @param string $list 
     * @return true|void 
     */
    protected static function GetHasOptions($command, string $list){        
        foreach(explode("|", $list) as $m){
            if (property_exists($command->options, $m)){
                return true;
            }
        }
    }

    public function __construct()
    {
        $this->handle = [$this, "exec"];
    }
    /**
     * run the command with args
     * @param mixed $args 
     * @param mixed $command source command options
     * @return mixed 
     */
    public function run($args, $command)
    {
        if ($this->handle){
            if ( $command->exec ){
                $command->options->{$args} = 1;
                return;
            }
            $command->exec = function($command){
                if (property_exists($command->options, "--help")){
                    $h= $this->help();
                    Logger::print("\n");
                    return $h;
                }
                DbCommandHelper::Init($command); 
                $fc = $this->handle;
                $args = func_get_args();
                return $fc(...$args);

            };
        }
    }
    public abstract function exec($command);

    /**
     * get controller helper
     * @param string $controller 
     * @param int $throwex 
     * @return mixed|BaseController  
     * @throws IGKException 
     */
    protected static function GetController(string $controller, $throwex = 1, $autoregister = true){
        $ctrl =  \IGK\Helper\SysUtils::GetControllerByName($controller, $throwex);
        $ctrl && $autoregister && $ctrl->register_autoload();
        return $ctrl;
    }
    protected function _dieController($command, ?string $controller, bool $system=false){
		if ($controller){
			if ($controller != '%sys%'){
				if ($ctrl = self::GetController($controller, false)){
                   
					return $ctrl;
				}
			} else {
				$system = true;
			}
		}
		return $system ? SysDbController::ctrl() : igk_die("controller required");
	}
}