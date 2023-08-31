<?php
// @author: C.A.D. BONDJE DOUE
// @file: SelectCommand.php
// @date: 20230725 12:03:45
namespace IGK\System\Console\Commands\Database;

use IGK\Helper\JSon;
use IGK\Models\ModelBase;
use IGK\System\Console\AppExecCommand;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Database
*/
class SelectCommand extends AppExecCommand{
	var $command='--db:select';
	var $desc='send a db select query or execute a Model Macros by choose a model. syntaxe for mode';	
	var $options=[
		'--count'=>'flag: count all entries for simple select',
		'--arg:[value]+'=>'argument for macros function'
	];
	var $category = 'db';  
	var $usage = 'controller model[.macrosFunction] [options]';
	public function exec($command, ?string $ctrl=null, ?string $model=null) { 
		is_null($ctrl) && igk_die("require controller");
		is_null($model) && igk_die("require model");


		$ctrl = self::GetController($ctrl); 
		$tab = explode('.',$model,2);
		$model = array_shift($tab);
		$m = $ctrl->model($model) ?? igk_die(sprintf("missing model - [%s]",$model));
		if (count($tab)>0){
			$args = igk_getv($command->options, '--arg', []);
			if ($method = trim(array_shift($tab))){
				if (!is_array($args)){
					$args = [$args];
				}
				// + | execute a model macros
				$g = call_user_func_array([$m, $method], $args);
				if ($g){
					self::PrintResult($g);
				}
				igk_exit();
			}
		}

		$count = property_exists($command->options, '--count');
		if ($count){
			echo "count(*) ".$m->count() .PHP_EOL;
			igk_exit();
		}

		$g = $m->select_all();
		echo JSon::Encode($g); //->to_json();
		igk_exit();

	}
	/**
	 * print result 
	 * @param mixed $g 
	 * @return void 
	 */
	public static function PrintResult($g){
		if ($g instanceof ModelBase){
			echo $g->to_json();
			return;
		}
		if (is_bool($g) || is_numeric($g)){
			echo var_dump($g);
			return;
		}
		
		$f = 0;
		$h = [];
		foreach($g as $row){ 
			$f = 1;
			$p = array_keys($row->to_array());
			echo $row->to_json() . PHP_EOL;
		}
	}
}