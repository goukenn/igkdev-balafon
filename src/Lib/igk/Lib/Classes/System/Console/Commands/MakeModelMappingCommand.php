<?php
// @author: C.A.D. BONDJE DOUE
// @file: MakeModelMappingCommand.php
// @date: 20240916 16:33:43
namespace IGK\System\Console\Commands;

use IGK\Database\Mapping\SysDbMapping;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\EntryClassResolution;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\StringBuilder;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class MakeModelMappingCommand extends AppExecCommand{
	var $command='--make:model-mapping';
	var $desc='make model mapping'; 
	var $options=[]; 
	var $category = 'make';

	public function exec($command, $model_name =null, $controller=null) {
		$ctrl = self::ResolveController($command, $controller);
		$model = $ctrl->model($model_name);
		if (!$model){
			igk_die('missing model');			
		}
		$clname = igk_str_add_suffix(ucfirst(igk_str_ns($model_name)), "Mapping");
		$author = $this->getAuthor($command);
		$path = igk_uri(EntryClassResolution::DbClassMapping."/".$clname);
		$ns = $ctrl->getEntryNamespace()."/".EntryClassResolution::DbClassMapping;
		if (!empty($ns)) {
            $ns = str_replace("/", "\\", $ns);
        }
		$bind[$ctrl::classdir() . "/".$path. ".php"] = function ($file) use ($clname, $author, $ns) {
            $builder = new PHPScriptBuilder();
            $fname = basename($file);
            $builder->type("class")->name($clname)
                ->author($author) 
                ->doc("mapping")
                ->file($fname)
                ->namespace($ns)
                ->extends(SysDbMapping::class)
                ->desc("mapping " . $clname);
            igk_io_w2file($file,  $builder->render());
        };

		Utility::MakeBindFiles($command, $bind, false);
		Logger::success("done\n");
	}
}