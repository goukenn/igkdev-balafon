<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssProjectThemeCssColorAndPropertyExportCommand.php
// @date: 20241030 15:22:04
namespace IGK\System\Console\Commands\Css;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Colorize;
use IGK\System\Console\Logger;
use IGK\System\Html\Css\CssConstants;
use IGK\System\Html\Css\CssUtils;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\IO\Path;

///<summary></summary>
/**
* use to extract color and properties form a css distribution 
* @package IGK\System\Console\Commands\Css
* @author C.A.D. BONDJE DOUE
*/
class CssProjectThemeCssColorAndPropertyExportCommand extends AppExecCommand{
	var $command='--project:css-export';
	/* var $desc='use to export css properties/color'; */
	var $options=[]; 
	var $category = 'project-css';
	var $usage = 'controller [option]';
	public function exec($command, ?string $controller=null) {
		$ctrl = self::ResolveController($command, $controller, false) ?? igk_die('required controller'); 
		$theme = in_array($theme = igk_getv($command->options, '--theme'), 
		explode('|', CssConstants::SUPPORT_THEME)) ? $theme : 'dark'; 
		$l = CssUtils::ExportColorAndProperties($ctrl, $theme); 
		Logger::SetColorizer(new Colorize);
		Logger::print(
			json_encode((object)$l, JSON_PRETTY_PRINT))
		;
	 }
}