<?php
// @author: C.A.D. BONDJE DOUE
// @file: BackupTablesCommand.php
// @date: 20241004 09:10:06
namespace IGK\System\Console\Commands\Database;
use IGK\Helper\IO;
use IGK\Helper\JSon;
use IGK\Helper\JSonEncodeOption;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\Import\DbModelImporterMap;
use IGK\System\IO\Path;
use IGK\System\IToArray;
use IGK\System\Regex\Replacement;

/**
* auto generate doc.
* @package IGK\System\Console\Commands\Database
* @author C.A.D. BONDJE DOUE
*/
class BackupTablesCommand extends AppExecCommand
{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = '--db:backup-tables';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'backup tables controller';
    /**
    * Property: options.
    * @var mixed
    */
    var $options = ['--restore' => 'flag to be in restore mode'];
    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'db';
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'controller outdir [options]';
    /**
    * Prefix handler.
    * @param string $back_name
    * @param null|array $attr
    */
    public function prefixHandler(string $back_name, ?array $attr=null){
		$regex = new Replacement;
		$regex->add("/%d/", date('Ymd'));
		$regex->add("/%n/", igk_getv($attr, 'n'));
		return $regex->replace($back_name);
	}
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $controller
    * @param null|string $outdir
    */
    public function exec($command, ?string $controller = null, ?string $outdir = null)
	{
		$ctrl = self::ResolveController($command, $controller, true);
		$_log = Logger::offscreen();
		$_is_debug = igk_is_debug();
		$restore_mode = property_exists($command->options, '--restore');
		$path = $outdir ?? Path::Combine(getcwd(), $this->prefixHandler('backup_%d_%n',['n'=>$ctrl->getName()])); 
		if ($tables = $ctrl->getDbDefinitionTables()) {
			$ad = $ctrl->getDataAdapter();
			IO::CreateDir($path);
			$option = JSonEncodeOption::IgnoreEmpty();
			if ($restore_mode){
				$ad->setForeignKeyCheck(0);
			}
			foreach (array_keys($tables) as $t) {
				$outfile = $path . '/' . $t . '.json';
				if ($restore_mode) {
					if (igk_io_file_exists($outfile)&&($model = $tables[$t]->model())) {
						if ($_is_debug) {
							$_log->info('restore: ' . $outfile);
						}	
						if ($data = json_decode(file_get_contents($outfile))){
							$mapping = DbModelImporterMap::CreateFrom($model);
							$mapping->autoregister = true;
							$mapping->transformField = false;
							$mapping->handleError=true;
							array_map($mapping, $data); 
						}					 
					}
					continue;
				}
				$q = $ad->selectAll($t);
				if (($q->getRowCount() > 0) && ($q instanceof IToArray)) {
					$json = JSon::Encode($q->to_array(), $option);
					if ($_is_debug) {
						$_log->info('backup: ' . $t);
						$_log->info('outfile: ' . $outfile);
					}
					igk_io_w2file($outfile, $json);
				}
			}
			if ($restore_mode){
				$ad->setForeignKeyCheck(1);
			}
		}
	}
}