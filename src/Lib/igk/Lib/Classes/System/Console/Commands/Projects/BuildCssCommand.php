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
use IGK\System\Html\Css\CssUtils;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\IO\Path;

/**
 * auto generate doc.
 * @package IGK\System\Console\Commands\Projects
 * @author C.A.D. BONDJE DOUE
 */
class BuildCssCommand extends AppExecCommand
{
	/**
	 * Property: command.
	 * @var mixed
	 */
	var $command = '--project:build-css';
	/**
	 * Property: desc.
	 * @var mixed
	 */
	var $desc = 'css. generate project\'s style theme';
	/**
	 * Property: options.
	 * @var mixed
	 */
	var $options = [
		'-f:file' => 'file to parse',
		'-d:dir' => 'relative directory',
		'-o:file' => 'output where to store',
	];
	/**
	 * Property: category.
	 * @var mixed
	 */
	var $category = "project";
	/**
	 * Property: usage.
	 * @var mixed
	 */
	var $usage = 'controller [file] [options]';
	/**
	 * Exec.
	 * @param mixed $command
	 * @param null|string $controller
	 * @param null|string $file
	 */
	public function exec($command, ?string $controller = null, ?string $file = null)
	{
		$ctrl = self::GetController($controller);
		$v_dir = $ctrl->getDeclaredDir();
		if ($d = igk_getv($command->options, '-d')) {
			if (!is_array($d)) {
				$d = [$d];
			}
			foreach ($d as $k => $v) {
				$d[$k] = Path::Combine($v_dir, $v);
			}
		}

		$dirs = $d ?? [$v_dir, $ctrl->getArticlesDir()];
		if ($dirs && !is_array($dirs)) {
			$dirs = [$dirs];
		} else {
			$dirs = array_unique($dirs);
		}
		$const_cache_file = igk_io_cachedir() . '/css/.fs-core-cache.css';
		$output = "dist";
		$name = "main.css";
		$out = Path::Combine($ctrl->getAssetsDir(), $output);
		$detector = new CssClassNameDetector;
		$source = [];
		$main_doc = igk_app()->getDoc();
		if (igk_io_file_exists($const_cache_file, true)) {
			$source = json_decode(file_get_contents($const_cache_file), true);
		} else {
			$core = igk_css_doc_get_def($main_doc);
			$source = CssParser::Parse($core)->to_array();
			igk_io_w2file($const_cache_file, json_encode($source));
		}
		$detector->map($source);
		//+| build controller style definition 
		$builder_style = igk_css_render_controller_style($ctrl, $main_doc);
		$source = CssParser::Parse($builder_style)->to_array();
		$detector->map($source);

		$r = $file ?? igk_getv($command->options, '-f');
		if ($r && !is_file($r)) {
			igk_die('missing file');
		}
		if ($r) {
			$g = file_get_contents($r);
			$source = CssParser::Parse($g);
			$detector->map($source->to_array());
		}
		$resolved_def = [];
		$references = [];
		$css_m = CssUtils::GetCssClassName($ctrl);
		while (count($dirs) > 0) {
			if ($c = array_shift($dirs)) {
				foreach (igk_io_getfiles($c, "/\.(phtml|html|bview)$/", true) as $f) {
					if ($r = CssClassNameDetectorUtils::DetectFromFile($detector, $f, $references)) {
						$resolved_def = array_merge($r, $resolved_def);
					}
				}
			}
		}
		$inject_references = [
			'igk-powered', // powered node definition 
			'igk-body', // body presentation 
			'no-contextmenu', 
			'no-overflow', 
			'no-scroll'
		];
		if ($css_m) {
			$inject_references[] = $css_m;
		}
		if ($inject_references)
		$detector->loadReferences($inject_references, $references);

		$outfile = igk_getv($command->options, '-o') ??  Path::Combine($out, 'css', $name);
		if ($references) {
			$option = (object)['lf' => ''];
			igk_io_w2file($outfile, $this->getCoreCss() . $detector->renderToCss($references, $option));
			Logger::success("output: " . $outfile);
		} else {
			Logger::warn('no css reference detected. use of css');
		}
	}
	public function getCoreCss()
	{
		return implode('', [
			'*{margin:0; padding: 0; box-sizing: content-box;}',
			'body, html{ width:100%; height:100%;}',
			$this->getSysThemeMedia(),
		]);
	}
	/**
	 * retrieve system media theme
	 * @return null|string 
	 */
	public function getSysThemeMedia()
	{
		$theme = null;
		$sys_theme = igk_css_reg_mediatype($theme);
		return $sys_theme;
	}
}
