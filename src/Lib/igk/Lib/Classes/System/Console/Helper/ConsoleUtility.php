<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConsoleUtility.php
// @date: 20230616 11:12:43
namespace IGK\System\Console\Helper;

use Closure;
use IGK\Helper\Utility;
use IGK\System\Console\App;
use IGK\System\Console\AppCommand;
use igk\System\Console\Commands\Utility as CommandsUtility;
use IGK\System\Console\Logger;
use IGKBacktickHelperCommandTrait;

require_once IGK_LIB_CLASSES_DIR . '/IGKBacktickHelperCommandTrait.php';
/**
 * 
 * @package IGK\System\Console\Helper
 */
abstract class ConsoleUtility
{
    /**
     * 
     * @return null|string 
     */
    public static function GetClipboardData(): ?string{
        // + | GET CLIP BOARD DATA
        $c = null;
		switch (strtolower(PHP_OS)) {
			case 'darwin':
				# code...
				$c = shell_exec('pbpaste');
				break;
			default:
				if (!igk_environment()->isUnix()){
					$c = shell_exec('powershell Get-Clipboard');
				} else{
					$c = shell_exec('xclip -o -selection clipboard');
				}
				# code...
				break;
		}
        return $c;
    }
    /**
     * show db result 
     */
    static function ShowJSonDdResult($result)
    {
        echo ($result ? Utility::TO_JSON($result, null, JSON_PRETTY_PRINT) : '') . PHP_EOL;
    }
    

    const OPTIONS_TAB_SPACE = AppCommand::OPTIONS_TAB_SPACE;
    use IGKBacktickHelperCommandTrait;

    /**
     * bind and make file 
     * @param array $bind 
     * @param mixed $command 
     * @return bool 
     */
    static function MakeFiles(array $bind, $command, bool $force = false)
    {
        return CommandsUtility::MakeBindFiles($command, $bind, $force);
    }
  /**
     * bind files 
     * @param mixed $command 
     * @param mixed $bind 
     * @param bool $is_force 
     * @return bool
     */
    public static function MakeBindFiles($command, $bind, $is_force = false): bool
    {
        $gen = false;
        foreach ($bind as $n => $c) {
            if ($is_force || !igk_io_cache_file_exists($n)) {
                $gen = true;
                if ($c instanceof Closure)
                    $c($n, $command);
                else {
                    $code = is_string($c) ? $c : '';
                    igk_io_w2file($n, $code);
                }
                Logger::info("generate : " . $n);
            }
        }
        return $gen;
    }
    /**
     * 
     * @param mixed $opts 
     * @param mixed $color_one 
     * @param mixed $color_two 
     * @return void 
     */
    public static function PrintCommand($opts, $color_one = App::AQUA, $color_two = App::GREEN)
    {
        foreach ($opts as $k => $v) {
            if (empty($v) && (strpos($k, '+') === 0)) {
                Logger::print(App::Gets($color_one, $k));
                Logger::print('');
                continue;
            }
            Logger::print(App::Gets($color_two, $k) . self::OPTIONS_TAB_SPACE . "{$v}" . PHP_EOL);
        }
    }
    /**
     * touch and override 
     * @param string $content 
     * @param bool $override 
     * @return Closure 
     */
    public static function TouchFileCallback($content = "", bool $override = true)
    {
        return function ($file) use ($content, $override) {
            return igk_io_w2file($file, $content, $override);
        };
    }
  
    /**
     * build package json author
     * @param mixed $command 
     * @return object 
     */
    public static function PackageJsonAuthor($command)
    {
        $name = $command->app->getAuthor();
        $email = IGK_AUTHOR_CONTACT;
        return (object)['email' => $email, 'name' => $name];
    }

    /**
     * determine if argument list avec arguments
     * @param array $arg 
     * @return bool 
     */
    public static function HaveArg(array $arg):bool{
        while(count($arg)){
            $q = array_shift($arg);
            if (!preg_match("/^-/", $q)){
                return true;
            }
        }
        return false;
    }

    /**
     * build argument 
     * @param array $arg 
     * @return string 
     */
    public static function BuildArgs(array $arg): string
    {
        $cm = implode(' ', array_filter(array_map(function ($v, $k) {
            $n_k = is_numeric($k);
            if (is_null($v)) {
                if (!$n_k) {
                    return $k;
                }
                return null;
            }
            if ($n_k) {
                return $v;
            }
            return implode(':', [$k, $v]);
        }, $arg, array_keys($arg))));
        return $cm;
    }
}
