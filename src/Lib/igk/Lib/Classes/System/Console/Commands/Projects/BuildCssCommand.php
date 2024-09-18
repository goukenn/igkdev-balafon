<?php
// @author: C.A.D. BONDJE DOUE
// @file: BuildCssCommand.php
// @date: 20240913 12:26:10
namespace IGK\System\Console\Commands\Projects;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Html\Css\CssClassNameDetector;
use IGK\System\Html\Css\CssClassNameDetectorUtils;
use IGK\System\Html\Css\CssParser;
use IGK\System\IO\Path;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Projects
* @author C.A.D. BONDJE DOUE
*/
class BuildCssCommand extends AppExecCommand{
	var $command='--project:build-css';
	/* var $desc='desc'; */
	/* var $options=[]; */
	var $category = "project-build";
	public function exec($command, ?string $controller=null) {
		$ctrl = self::GetController($controller);
		$dirs = [$ctrl->getViewDir(),$ctrl->getArticlesDir()];
		$output = "dist";
		$name = "main.css";
		$out = Path::Combine($ctrl->getDeclaredDir(), $output);
		$detector = new CssClassNameDetector;
		$core = igk_css_doc_get_def(igk_app()->getDoc());
		$source = CssParser::Parse($core);		
		$detector->map($source->to_array());

		// library to include :
		 $g = file_get_contents("/Volumes/Data/wwwroot/core/Projects/WatchCssDemo/node_modules/bootstrap/dist/css/bootstrap.css");
		 $source = CssParser::Parse($g);		
		 $detector->map($source->to_array());
 



		$resolved_def = [];
		$references = [];
		while(count($dirs)>0){
			$c = array_shift($dirs);
			foreach(igk_io_getfiles($c, "/\.(phtml|html|bview)$/", true) as $f){
				if ($r = CssClassNameDetectorUtils::DetectFromFile($detector, $f, $references)){
					$resolved_def = array_merge($r, $resolved_def);
				}
			}
		}
		if ($references)
		{
			$option = (object)['lf'=>''];
			$outfile = Path::Combine($out,'css', $name);
			igk_io_w2file($outfile, $detector->renderToCss($references, $option));
			Logger::success("output: ".$outfile);
		}
		else{
			Logger::warn('no use of css');
		}
	}
}