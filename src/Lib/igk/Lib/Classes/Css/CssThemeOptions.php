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
    const DEFAULT_THEME_NAME = "dark";
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
    public function __construct()
    { 
    }
}