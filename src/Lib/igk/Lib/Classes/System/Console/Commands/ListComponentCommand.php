<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListComponentCommand.php
// @date: 20230319 07:39:48
namespace IGK\System\Console\Commands;
use IGK\Helper\PhpHelper;
use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Regex\Replacement;
use ReflectionException;
use IGKException;
use ReflectionFunction;

/**
 * 
 * @package IGK\System\Console\Commands
 */
/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class ListComponentCommand extends AppExecCommand
{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = '--list:components';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'list installed component';
    /**
    * Property: options.
    * @var mixed
    */
    var $options=[
		"--count"=>"flag: show number of definded functions",
		"--files"=>"flag: group with file",
		"--info"=>"flag: show info",
	];
    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'winui';
    /**
    * Shows Usage.
    */
    function showUsage()
	{
		parent::showUsage();
		Logger::info(sprintf('%s [pattern] [options]', $this->command));
	}
    /**
    * auto generate doc.
    * @param mixed $command
    * @param string|null $pattern
    * @return void
    */
    public function exec($command, ?string $pattern=null)
	{
		if (!is_null($pattern)){
			$pattern = Replacement::RegexExpressionFromString($pattern);
		}
		$g = igk_sys_get_html_components($pattern); 
		$T = count($g);
		$map = function($a){
			return $a;
		};
		$f_file = property_exists($command->options , '--files');
		$f_info = property_exists($command->options , '--info');
		if ($f_file || $f_info){
			$tab = [];
			$info = [];
			$_info = $f_info;
			foreach($g as $fc ){
				$ref = new ReflectionFunction(IGK_FUNC_NODE_PREFIX.$fc);
				$info = [];
				$fn = $ref->getFilename();
				if (!isset($tab[$fn])){
					$tab[$fn] = [];
				}
				if ($_info){
					$params = $ref->getParameters();
					$s = $params? PhpHelper::GetParamerterDescription($params) : '';
					$info[] = "(".$s.")";
					$info[] = implode("\n", array_map('trim', explode("\n", $ref->getDocComment())));
				}
				$tab[$fn][] = $fc. ($_info? "\n".implode("\n", $info) : null);
			}
			ksort($tab);
			$g = $tab;
			$map = function($a, $k){
				$s = App::Gets(App::GREEN, $k). PHP_EOL;
				sort($a);
				return $s.implode("\n", $a) . PHP_EOL;
			};
		}
		echo implode("\n", array_map($map, $g, array_keys($g)));		
		echo PHP_EOL;
		if (property_exists($command->options , '--count')){
			Logger::info("Count : ".$T);
		}
	}
}