<?php
// @author: C.A.D. BONDJE DOUE
// @file: SelectCommand.php
// @date: 20230725 12:03:45
namespace IGK\System\Console\Commands\Database;

use IGK\Helper\JSon;
use IGK\Helper\SysUtils;
use IGK\Models\ModelBase;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\Mapping\DefaultMap;
use IGK\System\Database\Mapping\MappedData;
use IGK\System\IO\Configuration\ConfigurationReader;
use IGK\System\Mapping\Helper\ArrayMapHelper;

///<summary></summary>
/**
 * 
 * @package IGK\System\Console\Commands\Database
 */
class SelectCommand extends AppExecCommand
{
	var $command = '--db:select';
	var $desc = 'send a db select query or execute a Model Macros by choose a model. syntaxe for mode';
	var $options = [
		'--count' => 'flag: count all entries for simple select',
		'--limit:from[,size]' => 'limit query result',
		'--order:column|order,...' => 'order query',
		'--columns:column,...' => 'limit selected columns',
		'--map:column=map,...' => 'map list column',
		'--like:expression' => 'select with search expression.',
		'--arg:[value]+' => 'argument for macros function',
	];
	var $category = 'db';
	var $usage = 'controller model[.macrosFunction] [options]';
	public function exec($command, ?string $ctrl = null, ?string $model = null)
	{
		is_null($ctrl) && igk_die("require controller");
		is_null($model) && igk_die("require model");

		$limit = igk_getv($command->options, '--limit');
		$order = igk_getv($command->options, '--order');
		$columns = igk_getv($command->options, '--columns');
		$like = igk_getv($command->options, '--like');
		$map = igk_getv($command->options, '--map');
		if ($limit) {
			$limit = array_map([ArrayMapHelper::class, 'DieNumberMap'], explode(',', $limit, 2));
		}

		$ctrl = self::GetController($ctrl);
		$tab = explode('.', $model, 2);
		$model = array_shift($tab);
		$m = $ctrl->model($model) ?? igk_die(sprintf("missing model - [%s]", $model));
		if (count($tab) > 0) {
			$args = igk_getv($command->options, '--arg', []);
			if ($method = trim(array_shift($tab))) {
				if (!is_array($args)) {
					$args = [$args];
				}
				// + | execute a model macros
				$g = call_user_func_array([$m, $method], $args);
				if ($g) {
					self::PrintResult($g);
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
			$order = explode(',', $order);
			$options['OrderBy'] = $order;
		}
		if ($columns) {
			$options['Columns'] = explode(',', $columns);
		}
		$v_cond = null;
		if ($like) {
			$conf = new ConfigurationReader;
			$like = $conf->read($like);
			$v_cond = (array)$like;
		}
		$g = $m->select_all($v_cond, $options);
		if ($map) {
			// mapping
			$v_conf = new ConfigurationReader;
			$map = $v_conf->read($map);
			$g = DefaultMap::MapModelData($map, $g);
		}
		echo JSon::Encode($g); //->to_json();
		igk_exit();
	}
	/**
	 * print result 
	 * @param mixed $g 
	 * @return void 
	 */
	public static function PrintResult($g)
	{



		if ($g instanceof ModelBase) {
			echo $g->to_json();
			return;
		}
		if (is_bool($g) || is_numeric($g)) {
			echo var_dump($g);
			return;
		}
		if (is_array($g)) {
			if (igk_array_is_assoc($g)) {
				array_map(function ($v, $k) {
					print_r(sprintf("%s\r\t\t\t=\r\t\t\t\t%s\n", $k, str_pad($v, 20, " ", STR_PAD_LEFT)));
				}, $g, array_keys($g));
				return;
			}
		}
 
		if (is_string($g)) {
			Logger::print($g);
		} else {
			foreach ($g as $row) {
				// $f = 1;
				if (is_object($row) && method_exists($row, "to_json")) {
					// $p = array_keys($row->to_array());
					echo $row->to_json() . PHP_EOL;
				} else {
					print_r(JSon::Encode($row));
				}
			}
		}
	}
}
