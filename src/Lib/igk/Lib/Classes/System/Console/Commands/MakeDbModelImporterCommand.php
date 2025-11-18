<?php
// @author: C.A.D. BONDJE DOUE
// @file: MakeDbModelImporterCommand.php
// @date: 20250225 12:14:36
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\Import\DbModelImporterMap;
use IGK\System\EntryClassResolution;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\StringBuilder;
/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class MakeDbModelImporterCommand extends AppExecCommand{
	var $command='--make:db-model-importer';
	var $desc='make database model importer';
	var $options=[
		'--force'=>'flag: force creations'
	];
	var $category = 'make';
	var $usage = 'controller model [class]';
	public function exec($command, $controller=null, $model=null) { 
		($model ) || igk_die('required model');
		$ctrl = self::ResolveController($command, $controller);
		$mod = $ctrl->model($model);
		if (!$mod){
			igk_die('missing model');			
		}
		$force = property_exists($command->options, '--force' );
		$clname = igk_str_add_suffix(ucfirst(igk_str_ns($model)), EntryClassResolution::ImportMappingSuffix );
		$author = $this->getAuthor($command);
		$h_ns = EntryClassResolution::DbClassImport;
		$path = igk_uri($h_ns."/".$clname);
		$ns = $ctrl->getEntryNamespace()."/".$h_ns;
		if (!empty($ns)) {
            $ns = str_replace("/", "\\", $ns);
        }
		$bind[$ctrl::classdir() . "/".$path. ".php"] = function ($file) use ($clname, $author, $ns) {
            $builder = new PHPScriptBuilder();
            $fname = basename($file);
			$sb = new StringBuilder();
			$sb->appendLine([ 
				"/**",
				"* entry import data",
				"*/",
				"protected function _onImportData(array \$data){",
				"\treturn parent::_onImportData(\$data);",
				"}"
			]);
            $builder->type("class")->name($clname)
                ->author($author) 
                ->file($fname)
                ->namespace($ns)
				->defs($sb.'')
                ->extends(DbModelImporterMap::class)
                ->desc("importer from  " . $clname);
            igk_io_w2file($file,  $builder->render());
        }; 
		Utility::MakeBindFiles($command, $bind, $force);
		Logger::success("done");
	}
}