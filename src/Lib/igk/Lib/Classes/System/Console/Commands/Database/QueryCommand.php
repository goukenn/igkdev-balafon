<?php
// @author: C.A.D. BONDJE DOUE
// @file: QueryCommand.php
// @date: 20231005 12:55:22
namespace IGK\System\Console\Commands\Database;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
/**
* 
* @package IGK\System\Console\Commands\Database
*/
class QueryCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--db:query';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='model fetch query';
	/* var $options=[]; */

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category="sys:db";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = "model [controller] [options]";

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $model
    * @param null|string $controller
    */
    public function exec($command, ?string $model=null, ?string $controller = null) {
		$ctrl = null;
		if ($controller){
			$ctrl = self::GetController($controller) ?? igk_die('missing controller');
		}
		is_null($model) && igk_die('missing model');
		$model = ($ctrl ? $ctrl->model($model) : null) ??  igk_ns_name($model);
		if (is_string($model) && !class_exists($model)) {
			Logger::danger("class not exists");
			return 0;
		}
		echo "[";
		$ch='';
		foreach ($model::select_fetch() as $o) {
			igk_wl($ch.$o->to_json().PHP_EOL);
			$ch = ',';
		}
		echo "]";
	 }
}