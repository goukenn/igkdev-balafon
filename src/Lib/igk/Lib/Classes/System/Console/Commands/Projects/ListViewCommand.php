<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListViewCommand.php
// @date: 20250513 15:24:46
namespace IGK\System\Console\Commands\Projects;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
/**
* auto generate doc.
* @package IGK\System\Console\Commands\Projects
* @author C.A.D. BONDJE DOUE
*/
class ListViewCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command='--project:views';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='list view project';
	/* var $options=[]; */
    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'project';
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'controller [options]';
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $controller
    */
    public function exec($command, ?string $controller=null) {
		$ctrl = self::GetController($controller);
		$views = $ctrl::getViews(false,true);
		usort($views, function($a, $b){
			return strtolower($a) <=> strtolower($b);
		});
		Logger::info('list project\'s views'."\n");
		array_map(function($a)use($ctrl){
			Logger::print($a);
		}, $views);
		//Logger::success('done');
	}
}