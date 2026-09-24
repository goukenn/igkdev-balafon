<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssThemeBuilderOptions.php
// @date: 20260916 09:24:29
namespace IGK\System\Html\Css\Compiler;

use IGK\Constants;

/**
* 
* @package IGK\System\Html\Css\Compiler
* @author C.A.D. BONDJE DOUE
*/
class CssThemeBuilderOptions{
    /**
     * 
     * @var ?string
     */
    var $lf;

    /**
     * 
     * @var mixed
     */
    var $inlineTheme;
    /**
     * 
     * @var mixed
     */
    var $controller;

    /**
     * 
     * @var mixed
     */
    var $useCoreCss = true;

    /**
     * main file to bind 
     * @var mixed
     */
    var $main;

    /**
     * setup for theme color [dark=>[], light=>[]]
     * @var mixed
     */
    var $themeColors;

    /**
     * directory une in 
     * @var string|array
     */
    var $resolveDir;

    /**
     * imported class definition to import 
     * @var array
     */
    var $imported;

    /**
     * merge core imported
     * @var true
     */
    var $mergeCoreImported;

    public function __construct()
    {
        $this->main = Constants::DEFAULT_THEME_STYLE;
        $this->mergeCoreImported = true;
    }
}