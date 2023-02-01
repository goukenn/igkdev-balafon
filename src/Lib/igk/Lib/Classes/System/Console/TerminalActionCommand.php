<?php
// @author: C.A.D. BONDJE DOUE
// @filename: TerminalActionCommand.php
// @date: 20220421 08:02:53
// @desc: Terminal action command


namespace IGK\System\Console;

use Closure;
use Error;
use Exception;
use IGKEnvironment;

// terminal action command

/**
 * terminal action command
 * @package IGK\System\Console
 */
class TerminalActionCommand{
    /**
     * the primary command promp^t
     * @var string
     */
    protected $prompt = "\$tac > ";

    /**
     * store running command
     * @var array
     */
    protected $commands = [];

    protected $errors = [];

    /**
     * retun command line
     * @return int 
     */
    public function run(){
        $result = 0;
        register_shutdown_function([$this, 'onExit']);
        !defined('IGK_THROW_MISSING_MACROS_EXCEPTION') && define('IGK_THROW_MISSING_MACROS_EXCEPTION', 1);
        !defined('IGK_RUN_TAC_COMMAND') && define('IGK_RUN_TAC_COMMAND', 1);

        $stop = ['quit','exit'];
        $func = Closure::fromCallable([self::class, '_RunCommand']);
        // function(){
          
        // };
        do{
            $this->_clearLastErrors();

            $command = readline($this->prompt);
            $e = !in_array($command , $stop);
            if ($e && !empty($command)){ 
                array_unshift($this->commands, $command);
                readline_add_history($command);
                try{ 
                    $func($command); 
                }
                catch(Error $e){
                    Logger::danger("error append : ".$e->getMessage());
                }
                catch(Exception $e){
                    Logger::danger("exception append : ".$e->getMessage());
                }
            }  
        }
        while($e);
        return $result;
    }
    protected function onExit(){
        $this->_clearLastErrors();
    }
    private function _clearLastErrors(){ 
        if ($l_error = error_get_last()){
            error_clear_last();
            $this->errors += $l_error;
        }       
    }

    static function _RunCommand(){
        extract([
            "ctrl"=>igk_environment()->get(IGKEnvironment::CURRENT_CTRL),
            "user"=>igk_environment()->get(IGKEnvironment::CURRENT_USER)
        ]);
        return var_dump(@eval("return ".func_get_arg(0).";"));
    }
}