<?php
// @author: C.A.D. BONDJE DOUE
// @file: SelectCommand.php
// @date: 20230725 12:03:45
namespace IGK\System\Console\Commands\Database;
use igk;
use IGK\Controllers\BaseController;
use IGK\Database\DbColumnInfo;
use IGK\Database\IDbQueryResult;
use IGK\Helper\JSon;
use IGK\Helper\JSonEncodeOption;
use IGK\Helper\SysUtils;
use IGK\Models\ModelBase;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\BalafonApplication;
use IGK\System\Console\Logger;
use IGK\System\Database\Mapping\DefaultMap;
use IGK\System\Database\Mapping\MappedData;
use IGK\System\IO\Configuration\ConfigurationReader;
use IGK\System\IToJSon;
use IGK\System\Mapping\Helper\ArrayMapHelper;

/**
 * 
 * @package IGK\System\Console\Commands\Database
 */
/**
* auto generate doc.
* @package IGK\System\Console\Commands\Database
*/
class SelectCommand extends AppExecCommand
{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = '--db:select';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'send a db select query or execute a Model Macros by choose a model.';
    /**
    * Property: options.
    * @var mixed
    */
    var $options = [
		'--count' => 'flag: count all entries for simple select',
		'--limit:from[,size]' => 'limit query result',
		'--order:column|order,...' => 'order query',
		'--columns:column,...' => 'limit selected columns',
		'--map:column=map,...' => 'map list column',
		'--like:expression' => 'select with search expression.',
		'--arg:[value]+' => 'argument for macros function',
		'--pretty' => 'flag: pretty print json result',
		'--user:login' => 'set attached user',
		'--for:id' => 'id to resolve mocking reference',
		'--json:arg' => 'passing sigle json string definition',
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
    var $usage = '[controller] model[.macrosFunction] [options]';
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $ctrl
    * @param null|string $model
    */
    public function exec($command, ?string $ctrl = null, ?string $model = null)
	{
		if (property_exists($command->options, '--controller')) {
			$model = $ctrl;
			$ctrl = igk_getv($command->options, '--controller');
		}
		is_null($ctrl) && igk_die("require controller");
		is_null($model) && igk_die("require model");
		$v_sep = ',';
		$limit = igk_getv($command->options, '--limit');
		$order = igk_getv($command->options, '--order');
		$columns = igk_getv($command->options, '--columns');
		$like = igk_getv($command->options, '--like');
		$map = igk_getv($command->options, '--map');
		$for = igk_getv($command->options, '--for');
		$pretty = property_exists($command->options, '--pretty');
		if ($limit) {
			$limit = array_map([ArrayMapHelper::class, 'DieNumberMap'], explode($v_sep, $limit, 2));
		}
		$ctrl = self::GetController($ctrl);
		$flag = JSON_UNESCAPED_SLASHES + ($pretty ? JSON_PRETTY_PRINT : 0);
		BalafonApplication::BindCommandUser($command, $ctrl, $user);
		$tab = explode('.', $model, 2);
		$model = array_shift($tab);
		$m = $ctrl->model($model) ?? igk_die(sprintf("missing model - [%s]", $model));
		if (igk_getv($tab, 0) == '?') {
			return $this->showModelMacros($command, $ctrl, $m);
		}
		$v_private_fields = $m->getColumnPrivateFields();
		if (count($tab) > 0) {
			$args = null;
			if ($targ = igk_getv($command->options, '--json')){
				if ($args = igk_json_parse($targ)){
					$args = [$args];
				}
			}
			$args = $args ?? igk_getv($command->options, '--arg') ?? array_slice(func_get_args(), 3);
			if ($method = trim(array_shift($tab))) {
				if (!is_array($args)) {
					$args = [$args];
				}
				if ($for) {
					$v_kfor = $for;
					if (($tm = $m->resolve($v_kfor))) {
						$m = $tm;
					}
				}
				// + | execute a model macros
				$g = call_user_func_array([$m, $method], $args);
				if ($g) {
					self::PrintResult($g, $flag);
				}
				igk_exit();
			}
		}
		$count = property_exists($command->options, '--count');
		if ($count) {
			echo "count(*) " . $m->count() . PHP_EOL;
			igk_exit();
		}
		$options = [];
		if ($limit) {
			$options['Limit'] = $limit;
		}
		if ($order) {
			// + get command order 
			$order = explode($v_sep, $order);
			$options['OrderBy'] = $order;
		}
		if ($columns) {
			$options['Columns'] = explode($v_sep, $columns);
		}
		$v_cond = null;
		if ($like) {
			$conf = new ConfigurationReader;
			$like = $conf->read($like);
			$v_cond = (array)$like;
		}
		$v_ckeys = array_keys($v_private_fields);
		$options['@callback'] = function ($row) use ($v_ckeys) {
			foreach ($v_ckeys as $k) {
				unset($row->{$k});
			}
			return $row;
		};
		$g = $m->select_all($v_cond, $options);
		if ($map) {
			$v_conf = new ConfigurationReader;
			$map = $v_conf->read($map);
			$g = DefaultMap::MapModelData($map, $g);
		}
		echo JSon::Encode($g, JSonEncodeOption::IgnoreEmpty(), $flag), PHP_EOL; // + |
		igk_exit();
	}
    /**
    * auto generate doc.
    * @param mixed $command
    * @param BaseController $ctrl
    * @param ModelBase $model
    * @return int
    */
    public function showModelMacros($command, BaseController $ctrl, ModelBase $model)
	{
		$macros = $model->getMacroKeys($model);
		foreach ($macros as $s) {
			igk_wln($s . '');
		}
		return 0;
	}
    /**
    * print result
    * @param mixed $g
    * @param mixed $flag
    * @return void
    */
    public static function PrintResult($g, $flag)
	{
		if ($g instanceof ModelBase) {
			echo $g->to_json($flag);
			return;
		}
		if (is_bool($g) || is_numeric($g)) {
			echo var_dump($g);
			return;
		}
		if (is_array($g)) {
			echo JSon::Encode($g, JSonEncodeOption::IgnoreEmpty(), $flag);
			return;
		}
		if (is_string($g)) {
			Logger::print($g);
			echo PHP_EOL;
		} else {
			$r = [];
			if ($g instanceof IToJSon) {
				$r[] =  $g->to_json(null, $flag);
			} else {
				if ($g instanceof IDbQueryResult) {
					$g = $g->to_array();
				}
				foreach ($g as $row) {
					if (is_object($row) && method_exists($row, "to_json")) {
						$r[] = $row->to_json($flag);
					} else {
						$r[] = JSon::Encode($row, null, $flag);
					}
				}
			}
			echo sprintf('[%s]', implode(",", $r)) . PHP_EOL;
		}
	}
}