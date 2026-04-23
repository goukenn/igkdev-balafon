<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssThemeOptions.php
// @date: 20221008 14:15:11
namespace IGK\Css;

/**
* store default theme options
* @package IGK\System\Css
*/
class CssThemeOptions{
    /**
    * Constant: default theme name.
    * @var mixed
    */
    const DEFAULT_THEME_NAME = self::DARK_THEME_NAME;
    /**
    * Constant: dark theme name.
    * @var mixed
    */
    const DARK_THEME_NAME = "dark";
    /**
    * Constant: ligth theme name.
    * @var mixed
    */
    const LIGTH_THEME_NAME = "light";
    /**
    * Constant: both theme name.
    * @var mixed
    */
    const BOTH_THEME_NAME = "both";
    /**
     * is primary theme
     * @var mixed
     */
    var $is_primary;
    /**
     * theme name
     * @var ?string
     */
    var $theme_name;
    /**
     * array of skip definition in render mode 
     * @var ?array
     */
    var $skips;
    /**
    * auto generate doc.
    * @var ?ICssStoreRootListener
    */
    var $rootListener;
    /**
    * .ctr
    */
    public function __construct()
    { 
    }
}