<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExportToDataFileCommand.php
// @date: 20250226 11:28:16
namespace IGK\System\Console\Commands\Database;
use Exception;
use IGK\Helper\JSon;
use IGK\Helper\JSonEncodeOption;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\Import\DbModelImporterMap;
use IGKException;
/**
* 
* @package IGK\System\Console\Commands\Database
* @author C.A.D. BONDJE DOUE
*/

/**
* auto generate doc.
* @package IGK\System\Console\Commands\Database
*/
class ExportToDataFileCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command='--db:export';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='export structured data'; 
	/* var $options=[]; */

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'db';

    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'controller model outputfile [options]';

    /**
    * auto generate doc.
    * @param null|string $file
    * @return void
    */

    public function exec($command, ?string $controller=null, ?string $model=null, ?string $file=null) { 
		$ctrl = self::GetController($controller);
		!$model && igk_die('require model');		
		$file = $file ?? igk_die('required file');
		$model = $ctrl->model($model) ?? igk_die("missing model {$model}");
		$h = DbModelImporterMap::CreateFrom($model);
		$d = $h->export();
		$option = JSonEncodeOption::IgnoreEmpty();
		if ($d){
		igk_io_w2file($file, JSon::Encode($d, $option));
		Logger::print("store: ".$file);
		}
	}
}