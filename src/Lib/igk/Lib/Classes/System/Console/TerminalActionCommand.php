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
use function \readline;
/**
 * terminal action command
 * @package IGK\System\Console
 */
class TerminalActionCommand
{
    /**
     * the primary command promp^t
     * @var string
     */
    protected $prompt = App::BLUE . "\$tac >" . App::END . " ";
    /**
     * store running command
     * @var array
     */
    protected $commands = [];
    /**
    * Property: errors.
    * @var mixed
    */
    protected $errors = [];
    /**
     * retun command line
     * @return int 
     */
    public function run()
    {
        $result = 0;
        register_shutdown_function(function () {
            $this->onExit();
        });
        !defined('IGK_THROW_MISSING_MACROS_EXCEPTION') && define('IGK_THROW_MISSING_MACROS_EXCEPTION', 1);
        !defined('IGK_RUN_TAC_COMMAND') && define('IGK_RUN_TAC_COMMAND', 1);
        $stop = ['quit', 'exit'];
        $func = Closure::fromCallable([self::class, '_RunCommand']);
        if (!function_exists('readline')) {
            Logger::danger("require readline");
            return -1;
        }
        do {
            $this->_clearLastErrors();
            $command = igk_read_line($this->prompt);
            $e = !in_array($command, $stop);
            if ($e && !empty($command)) {
                array_unshift($this->commands, $command);
                readline_add_history($command);
                try {
                    $func($command);
                } catch (Error $e) {
                    Logger::danger("error append : " . $e->getMessage());
                } catch (Exception $e) {
                    Logger::danger("exception append : " . $e->getMessage());
                }
            }
        } while ($e);
        return $result;
    }
    /**
    * On exit.
    */
    protected function onExit()
    {
        $this->_clearLastErrors();
    }
    /**
    * auto generate doc.
    * @return
    */
    private function _clearLastErrors()
    {
        if ($l_error = error_get_last()) {
            error_clear_last();
            $this->errors[] = $l_error;
        }
    }
    /**
    * Run command.
    */
    static function _RunCommand()
    {
        extract([
            "ctrl" => igk_environment()->get(IGKEnvironment::CURRENT_CTRL),
            "user" => igk_environment()->get(IGKEnvironment::CURRENT_USER)
        ]);
        return var_dump(@eval("return " . self::_GetCommand(func_get_arg(0)) . ";"));
    }
    /**
    * auto generate doc.
    * @param string $cmd
    * @return ?string
    */
    private static function _GetCommand(string $cmd): ?string
    {
        if (preg_match("/^\s*echo\s+/", $cmd, $d)) {
            return substr($cmd, strlen($d[0]));
        }
        return $cmd;
    }
}