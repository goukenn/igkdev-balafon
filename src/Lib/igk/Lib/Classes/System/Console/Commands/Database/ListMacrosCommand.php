<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListMacrosCommand.php
// @date: 20240104 08:40:30
namespace IGK\System\Console\Commands\Database;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Models\ModelBase;
use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use PhpParser\Node\Expr\Instanceof_;
use ReflectionFunction;
use ReflectionMethod;

/**
 * 
 * @package IGK\System\Console\Commands\Database
 * @author C.A.D. BONDJE DOUE
 */
class ListMacrosCommand extends AppExecCommand
{

    /**
    * Property: command.
    * @var mixed
    */
    var $command = '--db:macros';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'list controller\'s model macros';

    /**
    * Property: options.
    * @var mixed
    */
    var $options=[
		'--all'
	];

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'db';

    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'controller modelName* [options]';

    /**
    * Exec.
    * @param mixed $command
    * @param null|string $controller
    * @param null|string $ModelName
    */
    public function exec($command, ?string $controller = null, ?string $ModelName = null)
	{
		$ctrl = $controller ? self::GetController($controller) : SysDbController::ctrl();
		if ($ctrl instanceof BaseController) {
			if ($ModelName) {
				$model = $ctrl->model($ModelName);
				$macros = $model->getMacroKeys();
				Logger::print(sprintf('List [%s] model macros', $ctrl->getName()));
				usort($macros, function ($a, $b) {
					return strcasecmp($a . '', $b . '');
				});
				$sep = ModelBase::StaticSeparator;
				$all = property_exists($command->options, '--all');
				$cl = get_class($model);
				foreach($macros as $m){
					$e = $m . '';
					$sb = '';
					$fn = null;
					if (strpos($e, $sep) !== false) {
						if (!$all && !igk_str_startwith($e, $cl.$sep)){
							continue;
						}
						$sb .= App::Gets(App::BLUE_B, 'e ' . "\t"); 
						$fn = $e;
					} else if ($all) {
						if (strpos($e, ModelBase::ClosureSeperator) !== false) {
							$sb .= App::Gets(App::GREEN, 'f ' . "\t");
							$fn = $e;
						} else {
							$sb .= App::Gets(App::YELLOW, 's ' . "\t");
						}
					} else {
						continue;
					}
					$sb .= $e;
					if ($fn && ($method = $model->getMacro($fn))) {
						if (is_callable($method) && ($method instanceof \Closure)) {
							$cl = ReflectionFunction::class;
							$params = (new $cl($method))->getParameters();
						} else {
							$params = (new ReflectionMethod(...$method))->getParameters();
						}
						$sb .= sprintf('[%s]',  implode(', ', array_map(function ($p) {
							$c = [];
							$c[] = '$' . $p->name;
							if ($p->hasType()) {
								array_unshift($c, $p->getType()->getName());
							}
							return implode(' ', $c);
						}, $params)));
					}
					Logger::print($sb);
				}
			} else {
				Logger::print('List all models and macros');
				$tabl = $ctrl::getModels();
			}
		}
		Logger::success('done');
	}
}
