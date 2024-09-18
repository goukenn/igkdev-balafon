<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExecModelUtilittyCommand.php
// @date: 20240917 19:29:12
namespace IGK\System\Console\Commands;

use IGK\Actions\Dispatcher;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Colorize;
use IGK\System\Console\Logger;
use ReflectionMethod;

///<summary></summary>
/**
* execute command db utility to handle action base on modelUtility 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class ExecModelUtilittyCommand extends AppExecCommand{
	var $command='--db:utility';
	var $desc='exec model db utility method';
	var $options=['--controller:controller'=>'set controller', 
	'--list'=>'list function'];
	var $category = 'db';
	var $usage = 'utilityName.methods ...args [options]';

	public function exec($command , ?string $utility_name_call = null ) { 
		if (is_null($utility_name_call)){
			igk_die('required model utility');
		}
		$ctrl = self::ResolveController($command);
		$args = array_slice(func_get_args(), 2);
		$names = explode('.', $utility_name_call, 2);
		// igk_wln_e("names", $names);
		// 
		if ($utility = $ctrl->modelUtility($names[0])){
			if (count($names)==1){
				if (property_exists($command->options, '--list')){
					return $this->showList($utility);
				}
			}
			$c = [$utility, $names[1]];
			$v_closure = \Closure::fromCallable($c);
			$v_tf = new \ReflectionFunction($v_closure);
			$params = Dispatcher::GetInjectArgs($v_tf, $args ?? []);
			$g = call_user_func_array($v_closure, $params);
			if ($g){
				Logger::GetColorizer() || Logger::SetColorizer(new Colorize);
				Logger::print(json_encode($g, JSON_PRETTY_PRINT));
				igk_exit(0);
			}
		}


		Logger::success('done');
	}
	public function showList($utility){
		$ref = igk_sys_reflect_class($utility);
		$l = [];
		array_map(function($a)use($ref, & $l){
			$acl = $a->getDeclaringClass()->name;
			$t = $ref->getName();
			if (!$a->isStatic() && !preg_match("/^_/", $a->name) && (( $acl == $t) || (is_subclass_of($acl, $t))))
			{
				$l[] = $a->name;
			}
		}, $ref->getMethods(ReflectionMethod::IS_PUBLIC));	
		sort($l);
		if (count($l)>0){
			Logger::info("LIST : \n");
			Logger::print(implode("\n", $l));
		}
	}

}