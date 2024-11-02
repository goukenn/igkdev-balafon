<?php
// @author: C.A.D. BONDJE DOUE
// @file: FaviconCommand.php
// @date: 20240925 16:28:38
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\IO\StringBuilder;
use IGK\System\Regex\RegexConstant;
use IGK\System\Regex\RegexHelper;

///<summary></summary>
/**
 * 
 * @package IGK\System\Console\Commands
 * @author C.A.D. BONDJE DOUE
 */
class FaviconCommand extends AppExecCommand
{
	var $command = '--favicon';
	/* var $desc='desc'; */
	var $options=[
		"--html"=>"flag: active html rendering",
		"--type:expected_type"=>"'base64' | 'html' | 'svg' | 'png' default is 'base64'"
	];
	/* var $category = ''; */
	var $usage = '[options]';

	private static function CheckType(string $type){
		if (in_array($type, explode("|", "base64|svg|html|png")))
			return $type;
		return null;
	}
	private static function GetType($command){
		if (property_exists($command->options, '--html'))
			return 'html';

	}
	public function exec($command)
	{
		$type = self::CheckType(igk_getv($command->options, "--type", '')) ?? self::GetType($command);
		if ($type=='png'){
			$file = IGK_LIB_DIR . '/Data/R/svg/favicon.png';
			echo 'data:image/png;base64,'.base64_encode(file_get_contents($file));
			// echo file_get_contents($file);
			return 0;
		}
		$file = IGK_LIB_DIR . '/Data/R/svg/favicon.svg';
		$_reduce = function($src){
			$src = implode(" ", explode("\n", $src));
			$src = base64_encode($src);
			$src = "data:image/svg+xml;base64," . $src ;
			return $src;
		};
		if (file_exists($file)) {
			$fcontent = file_get_contents($file);
			$src = ''; 
			switch($type)
			{
				case 'svg':
					$src = $fcontent;
					break;
				case 'png':

					break;
				case 'html': 
					$src = $_reduce($fcontent);
					$template = new ImageHtmlTemplate;
					$src = $template->treat([
						"title"=>"favicon",
						"src"=>$src
					]);
				break;
				default:
					$src = $_reduce($fcontent);
				break;
			}
			echo $src , "\n";
		}
		igk_exit();
	}
}
class ImageHtmlTemplate
{
	public function treat(array $data){
		return preg_replace_callback(RegexConstant::TEMPLATE_ARG_PLACEHOLDER_REGEX, function($m)use($data){
			return igk_getv($data, $m['name']);
		}, $this->render()); 
	}
	public function render()
	{
		$sb = new StringBuilder;
		$sb->appendLine(<<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ title }}</title>
</head>
<body>
    <img src="{{ src }}" alt="{{ title }}" />
</body>
</html>
HTML);

		return $sb . "";
	}
}
