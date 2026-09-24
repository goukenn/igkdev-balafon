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

        extract((array)$options);
        $ctrl = $controller;
        $g = '';
        $lf = $lf ?? '';
        if ($main) {
            igk_css_load_theme($theme); 
            igk_css_bind_file($theme, $ctrl, $main);
        }
        if ($v_theme_Colors = ($ctrl && is_null($themeColors) ? static::_GetThemeColor($ctrl, $theme) : $themeColors)) {
            $theme->setThemeColors($v_theme_Colors);
        }

        $v_sys_theme = igk_app()->getDoc()->getSysTheme();
        $clear = false;
        if (!$v_sys_theme->getinitGlobal()) {
            $clear = true;
            $v_sys_theme->initGlobalDefinition();
        }
        $v_maps = [];
        $v_sb = CssUtils::RenderAfterCheckColorTheme($theme, true);
        $theme_tab = CssParser::Parse($v_sb)->to_array();
        $v_maps[] = $theme_tab;
        if ($useCoreCss) {
            $imported = array_merge($imported ?? [], $mergeCoreImported? self::GetImported($theme, $theme_tab) : []);
            $tab = CssParser::Parse($v_sys_theme->render())->to_array();
            array_unshift($v_maps, $tab);
        }        
        $cwd = getcwd();
        chdir($ctrl->getDeclaredDir());
        $g .= CssClassNameDetector::RenderMaps(
            $v_maps,
            $imported,
            [
                'lf' => $lf,
                'resolveDir' => $resolveDir,
                'charsetHeader' => true,
            ]
        );
        chdir($cwd);

        if ($inlineTheme) {
            $v_bindTempFiles = $inlineTheme->getDef()->getBindTempFiles(0);
            if ($v_bindTempFiles) {
                igk_css_bind_theme_files($inlineTheme, $v_bindTempFiles);
                $g .= $inlineTheme->get_css_def(true, true);
            }
        }
        $sb[] = $g;
        if ($clear) {
            $v_sys_theme->resetSysGlobal();
        }
        $theme->resetAll();
        return implode($lf, $sb);
    }
    /**
     * 
     * @param BaseController $ctrl 
     * @param ?HtmlDocTheme $theme
     * @return array<string, mixed> 
     */
    protected static function _GetThemeColor(BaseController $ctrl, ?HtmlDocTheme $theme = null)
    {
        $theme_name = null;
        if ($theme) {
            $opt =  $theme->getRenderOptions();
            if ($opt) {
                $theme_name = $opt->theme_name;
            }
        }
        $theme_dir = $ctrl->getDeclaredDir() . '/Styles/themes';
        $fc_include = function () {
            extract(func_get_arg(1));
            $cl = [];
            include func_get_arg(0);
            return get_defined_vars();
        };
        $v_ttheme = [];
        $css_m = CssUtils::GetControllerClassSelector($ctrl);
        foreach (['dark', 'light'] as $v_tn) {
            if ($theme_name && ($v_tn == $theme_name)) {
                // $v_ttheme[$v_tn] = $theme->getCl();
                // continue;
            }
            if (file_exists($f = $theme_dir . '/' . $v_tn . '.theme.pcss')) {
                $def = $fc_include($f, ['theme_name' => $v_tn, 'ctrl' => $ctrl, 'css_m' => $css_m]);
                $v_ttheme[$v_tn] = $def['cl'];
            }
        }
        return $v_ttheme;
    }
    /**
     * retrieve imported core definition 
     * @param mixed $theme 
     * @param array $theme_tab 
     * @return array 
     */
    public static function GetImported($theme, array $theme_tab): array
    {
        $imported = array_merge(
            [
                '.dispn',
                '.fit',
                '.igk-body',
                '.igk-form',
                '.igk-bodybox',
                '.igk-device',
                '.igk-media-type',
                '.bold',
                '.italic',
                '.strikeout',
                '.underline',
                '.zigzag',
                '.double',
                '::selection',
                ':selection',
                ':root',
                '::root'
            ],
            $theme->getImportedDefinitions() ?? [],
            CssClassNameDetector::RetrieveInjectedClassesList($theme_tab) ?? [],
        );
        return array_unique($imported);
    }
}
