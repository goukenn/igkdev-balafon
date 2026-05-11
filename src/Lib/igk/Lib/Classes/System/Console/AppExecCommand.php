<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AppExecCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace  IGK\System\Console;
use Error;
use Exception;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKException;
use ReflectionException;

/**
* App exec command.
* @package IGK\System\Console
*/
abstract class AppExecCommand extends AppCommand{
    /**
    * Property: handle.
    * @var mixed
    */
    protected $handle;
    /**
    * Property: colorizer.
    * @var mixed
    */
    private $m_colorizer;
    /**
     * user category
     */
    const USER_CAT = 'users';
    /**
    * Constant: sys ctrl placeholder.
    * @var mixed
    */
    const SYS_CTRL_PLACEHOLDER = '%sys%';
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
     * bind user command
     * @param mixed $ctrl 
     * @param mixed $command 
     * @param string $arg 
     * @return void 
     * @throws Exception 
     */
    public static function BindUserCommand($ctrl, $command, $arg='--user'){
        $uref = null;
        if ($id = intval($uref = igk_getv($command->options, $arg))) {
            self::BindUser($ctrl, $id);
        }else if ($uref){
            if ($g = igk_get_user_bylogin($uref)){
                self::BindUser($ctrl, $g->clId);
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
    /**
     * get colorize 
     * @return Colorize 
     */
    protected function getColorizer(){
        return $this->m_colorizer ?? $this->m_colorizer = new Colorize;
        return new Colorize;
    }
    /**
     * initialize command
     * @return void 
     */
    public function __construct(){
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
    /**
    * Exec.
    * @param mixed $command
    */
    public abstract function exec($command);
    /**
    * get controller helper
    * @param string $controller
    * @param int $throwex
    * @param mixed $autoregister
    * @throws IGKException
    * @return mixed|BaseController
    */
    protected static function GetController(?string $controller, $throwex = 1, $autoregister = true){
        if (is_null($controller)){
            if ($throwex){
                igk_die("missing NIL controller");
            }
            return null;
        }
        $ctrl =  \IGK\Helper\SysUtils::GetControllerByName($controller, $throwex);
        $ctrl && $autoregister && $ctrl->register_autoload();
        return $ctrl;
    }
    /**
     * get controller or die
     * @param mixed $command 
     * @param null|string $controller 
     * @param bool $system 
     * @return mixed 
     * @throws IGKException 
     * @throws Error 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _dieController(?string $controller, bool $system=false){
		if ($controller){
			if ($controller != self::SYS_CTRL_PLACEHOLDER){
				if ($ctrl = self::GetController($controller, false)){ 
					return $ctrl;
				}
			} else {
				$system = true;
			}
		}
		return $system ? SysDbController::ctrl() : igk_die("controller required");
	}
    /**
    * resolve controller
    * @param mixed $command
    * @param mixed $controller
    * @param bool $fall_to_sys
    * @throws Exception
    * @return mixed
    */
    public static function ResolveController($command, $controller=null, bool $fall_to_sys=true){
        $controller = $controller ?? igk_getv($command->options, '--controller' );
		if ($controller){
			$ctrl = self::GetController($controller);
		}  
		$ctrl = $ctrl ?? ($fall_to_sys? SysDbController::ctrl() : null);
        return $ctrl;
    }
}