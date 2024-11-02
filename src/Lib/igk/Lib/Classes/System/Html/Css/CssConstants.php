<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssConstants.php
// @date: 20240213 19:30:53
namespace IGK\System\Html\Css;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssConstants{
    /**
     * core theme file extension.
     */
    const THEME_FILE_EXT = ".theme.pcss";
    const THEME_SELECTOR_PREFIX = 'html[data-theme=';
    const THEME_SELECTOR_FORMAT = self::THEME_SELECTOR_PREFIX. "'%s'] ";
    const SUPPORT_THEME = 'dark|light';
}