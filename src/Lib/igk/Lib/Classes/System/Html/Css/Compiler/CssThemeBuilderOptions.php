<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssThemeBuilderOptions.php
// @date: 20260916 09:24:29
namespace IGK\System\Html\Css\Compiler;


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
     * @var mixed
     */
    var $resolveDir;
}