<?php
// @author: C.A.D. BONDJE DOUE
// @file: InsertCommmandCommand.php
// @date: 20250822 10:30:16
namespace IGK\System\Console\Commands\Database;

use IGK\System\Console\AppExecCommand;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherPatternContainer;

/**
* 
* @package IGK\System\Console\Commands\Database
* @author C.A.D. BONDJE DOUE
*/
class InsertCommmandCommand extends AppExecCommand{
	var $command='--db:insert';
	var $desc='insert database in data manually ';
	var $options=[];
	var $category = 'db';
	var $usage = '[options]';
	public function exec($command, ?string $ctrl = null, ?string $model = null, ...$params) { 

		if (property_exists($command->options, '--controller')){
			$model = $ctrl;
			$ctrl = igk_getv($command->options, '--controller');
		}
		is_null($ctrl) && igk_die("require controller");
		is_null($model) && igk_die("require model"); 
		$ctrl = self::GetController($ctrl);
		$tab = explode('.', $model, 2);
		$model = array_shift($tab);
		$m = $ctrl->model($model) ?? igk_die(sprintf("missing model - [%s]", $model));
		
		$args = $params;

		while($args){
			$q = array_shift($args);
			$p = self::Format($q);

			// $r = array_map('trim', explode(';', $q)); 
			// if ($g = $m::Add(...$r)){
			// 	igk_wln_e('data: ', $g);
			// }
		}
		igk_wln_e($args);


	}
	/**
	 * - 
	 * @param string $src 
	 * @return void 
	 */
	public static function Format(string $src){
		$regex = new RegexMatcherContainer;
		$regex->match(';', 'split');
		$regex->match(',', 'separator');

		$pos=0;
		$voffset = 0;
		$v = '';
		// define
		while($g = $regex->detect($src, $pos)){
			if ($e = $regex->end($g, $src, $pos)){
				$sp = substr($src, $voffset, $e->from - $voffset);
				switch($e->tokenID){
					case 'split':
						break;
					case 'separator':
						break;
				}

				//$e->sp
				igk_wln('basic '. $sp);
			}
		}

	}
}