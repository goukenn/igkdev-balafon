<?php
// @author: C.A.D. BONDJE DOUE
// @file: LoadAssetsCommand.php
// @date: 20260317 13:40:49
namespace IGK\System\Console\Commands\Projects;
use IGK\Helper\IO;
use IGK\System\Console\AppExecCommand;
use IGK\System\IO\Path;

/**
* auto generate doc.
* @package IGK\System\Console\Commands\Projects
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Console\Commands\Projects
*/
class LoadAssetsCommand extends AppExecCommand{
	var $command='--project:store-asset';
    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $desc='store an asset to a project'; 
	/* var $options=[]; */
	/* var $category = ''; */
	/* var $usage = ''; */
    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $controller
    * @param null|string $file
    * @param mixed $algo
    * @return
    */
    public function exec($command, ?string $controller=null, ?string $file=null, $algo='crc32b') { 
		$ctrl = self::GetController($controller) ?? igk_die('missing controller');
		$dir = Path::Combine($ctrl->getDataDir(), 'assets');
		$algo || igk_die('missing hash algo');
		in_array($algo, hash_algos()) || igk_die('not defined algo');
		$guid = igk_create_guid();
		$mimetype = IO::MimeTypeFromFile($file);
		$asset = $ctrl->model('Assets');
		$hash = hash_file($algo, $file);
		$type = igk_getv([
			'image/jpeg'=>'jpg',
			'image/webp'=>'jpg',
		], $mimetype);
		$ext = $type ? '.'.$type : '.dat';
		$path = $type.'/'.$hash.$ext;
		$asset->insertIfNotExists([
			'guid'=>$guid,
			'mimetype'=>$mimetype,
			'hash'=>$algo.'|'.$hash,
			'path'=>$type.'/'.$hash.$ext,
		]);
		igk_io_w2file(Path::Combine($dir, $path), file_get_contents($file));
		igk_wln_e(compact('dir', 'guid', 'mimetype', 'asset'));
	}
}