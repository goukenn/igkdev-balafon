<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssThemeOptions.php
// @date: 20221008 14:15:11
namespace IGK\Css;


///<summary></summary>
/**
* store default theme options
* @package IGK\System\Css
*/
class CssThemeOptions{
    const DEFAULT_THEME_NAME = self::DARK_THEME_NAME;
    const DARK_THEME_NAME = "dark";
    const LIGTH_THEME_NAME = "light";
    const BOTH_THEME_NAME = "both";
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
     * 
     * @var ?ICssStoreRootListener
     */
    var $rootListener;
    public function __construct()
    { 
    }
}