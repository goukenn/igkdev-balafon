<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssThemeBuilder.php
// @date: 20260916 09:22:31
namespace IGK\System\Html\Css\Compiler;

use IGK\Controllers\BaseController;
use IGK\Helper\Activator;
use IGK\System\Html\Css\CssClassNameDetector;
use IGK\System\Html\Css\CssClassNameDetectorUtils;
use IGK\System\Html\Css\CssParser;
use IGK\System\Html\Css\CssUtils;
use IGK\System\Html\Dom\HtmlDocTheme;

/**
 * 
 * @package IGK\System\Html\Css\Compiler
 * @author C.A.D. BONDJE DOUE
 */
class CssThemeBuilder
{

    /**
     * build themet 
     * @param array|CssThemeBuilderOptions $options 
     * @return string 
     */
    public static function Build(HtmlDocTheme $theme, $options = [])
    {
        $sb = [];
        $options = Activator::CreateNewInstance(CssThemeBuilderOptions::class, $options ?? []);
        list($lf, $inlineTheme, $ctrl, $useCoreCss, $main, $themeColors, $resolveDir) = 
        igk_extract($options, 'lf|inlineTheme|controller|useCoreCss|main|themeColors|resolveDir');
        $g = '';
        $lf = $lf ?? '';
        if ($main){
            igk_css_bind_file($theme, $ctrl, $main);
        }
        igk_css_load_theme($theme);
        if ($ctrl) {
            $v_theme_Colors = $themeColors ?? static::_GetThemeColor($ctrl, $theme);
            $theme->setThemeColors($v_theme_Colors);
        }
        $v_sys_theme = igk_app()->getDoc()->getSysTheme();
        $clear = false;
        if (!$v_sys_theme->getinitGlobal()){
            $clear = true;
             $v_sys_theme->initGlobalDefinition();
        }
        $v_sb = CssUtils::RenderAfterCheckColorTheme($theme, true);


        if ($useCoreCss) {
            $theme_tab = CssParser::Parse($v_sb)->to_array();
            //if (!empty($theme_tab)) {
                $imported = array_merge(
                    [
                        'dispn',
                        'fit',
                        'igk-device',
                        'igk-media-type',
                        '::selection',
                        ':root'
                    ],
                    $theme->getImportedDefinitions() ?? [],
                    CssClassNameDetector::RetrieveInjectedClassesList($theme_tab) ?? [],
                );
                $tab = CssParser::Parse($v_sys_theme->render())->to_array();
                $g .= CssClassNameDetector::RenderMaps([
                   $tab,
                   $theme_tab,
                ], $imported, ['lf'=>$lf, 'resolveDir'=>$resolveDir]);
           // }
        }
        if ($inlineTheme) {
            $v_bindTempFiles = $inlineTheme->getDef()->getBindTempFiles(0);
            if ($v_bindTempFiles) {
                igk_css_bind_theme_files($inlineTheme, $v_bindTempFiles);
                $g .= $inlineTheme->get_css_def(true, true);
            }
        }
        $sb[] = $g;
        if ($clear){
            $v_sys_theme->resetSysGlobal();
        }
        return implode($lf, $sb);
    }
    protected static function _GetThemeColor(BaseController $ctrl)
    {
        $theme_dir = $ctrl->getDeclaredDir() . '/Styles/themes';
        $fc_include = function () {
            extract(func_get_arg(1));
            $cl = [];
            include func_get_arg(0);
            return get_defined_vars();
        };
        $v_ttheme = [];
        foreach (['dark', 'light'] as $theme) {
            if (file_exists($f = $theme_dir . '/' . $theme . '.theme.pcss')) {
                $def = $fc_include($f, ['theme_name' => $theme, 'ctrl' => $ctrl]);
                $v_ttheme[$theme] = $def['cl'];
            }
        }
        return $v_ttheme;
    }
}
