<?php
// @author: C.A.D. BONDJE DOUE
// @file: ZipDirCommand.php
// @date: 20231014 10:53:44
namespace IGK\System\Console\Commands;

use IGK\Helper\IO;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\Path;
use ZipArchive;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class ZipDirCommand extends AppExecCommand{
	var $command='--zip';
	var $desc='zip directory';
	var $options=[
		'--regex:[]'=>'exclude file pattern'
	]; 
	/* var $category; */
	public function exec($command, ?string $inputDir=null, string $outDir=null) {
		!is_dir($inputDir) && igk_die('missing inputdir');
		if (empty($outDir)){

			$outDir = Path::Combine(getcwd());
		}
		$filename = igk_getv($command->options, '--name' , basename($inputDir).date('Ymd_His').'.zip' );
		IO::CreateDir($outDir); 
		$outfile = Path::Combine($outDir, $filename); 
		$zip = new ZipArchive();
		if ($zip->open($outfile, ZipArchive::CREATE)){
			igk_is_debug() && Logger::info('zipping to '.$outfile);
			igk_zip_dir($inputDir,$zip); 
			$zip->close(); 
			Logger::success('done : '.$outfile);
		} else {
			Logger::danger('failed to create zip archive');
		}

	}
}