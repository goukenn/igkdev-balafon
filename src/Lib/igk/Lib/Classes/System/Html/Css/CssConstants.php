<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssConstants.php
// @date: 20240213 19:30:53
namespace IGK\System\Html\Css;

/**
* auto generate doc.
* @package IGK\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssConstants{
    const SUPPORT_COLOR_SPACES = 'srgb|srgb-linear|display-p3|display-p3-linear|a98-rgb|prophoto-rgbrec2020|rec2100-pq|rec2100-hlg|rec2100-linear|xyz|xyz-d50|xyz-d65|hsl|hwb|lch|oklch|shorter|longer|increasing|decreasing|hue';
    /**
     * core theme file extension.
     */
    const THEME_FILE_EXT = ".theme.pcss";
    /**
    * Constant: theme selector prefix.
    * @var mixed
    */
    const THEME_SELECTOR_PREFIX = 'html[data-theme=';
    /**
    * Constant: theme selector format.
    * @var mixed
    */
    const THEME_SELECTOR_FORMAT = self::THEME_SELECTOR_PREFIX. "'%s'] ";
    /**
    * Constant: support theme.
    * @var mixed
    */
    const SUPPORT_THEME = 'dark|light';
}