<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormatCommand.php
// @date: 20250809 09:24:32
namespace IGK\System\Console\Commands\Formatters;

use igk\phpFormatter\Formatters\HtmlFormatter;
use igk\phpFormatter\Formatters\HtmlFormatterService;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Text\Formatters\FormatterServiceContainer;
use IGKServices;
use PHPFormatter;

/**
* 
* @package IGK\System\Console\Commands\Formatters
* @author C.A.D. BONDJE DOUE
*/
class FormatCommand extends AppExecCommand{
	var $command='--format';
	var $desc='use to format code';
	var $options=[];
	var $category = 'code';
	var $usage = '';
	/**
	 * 
	 * @param mixed $command 
	 * @param null|string $file 
	 * @return void 
	 */
	public function exec($command, ?string $file=null) { 
		igk_assert_die(!$file, 'missing file');
		$l = IGKServices::FORMATTER_SERVICE;
		IGKServices::Register($l, FormatterServiceContainer::class);
		$ext = igk_io_path_ext($file);		
		IGKServices::Register($l.'.html', HtmlFormatterService::class);
 
		$service = igk_app()->getService('formatters.'.$ext); 
		if ($service){
			echo $service(file_get_contents($file));
		} else {
			Logger::danger('missing formatter');
		}
		Logger::success('complete');
	}
}