<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListViewCommand.php
// @date: 20250513 15:24:46
namespace IGK\System\Console\Commands\Projects;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
/**
* 
* @package IGK\System\Console\Commands\Projects
* @author C.A.D. BONDJE DOUE
*/
class ListViewCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--project:views';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='list view project';
	/* var $options=[]; */

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = 'project';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = 'controller [options]';

    /**
    * auto generate doc.
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