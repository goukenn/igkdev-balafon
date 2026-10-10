<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssConfiguration.php
// @date: 20260926 12:42:36
namespace IGK\System\Html\Css;

use IGK\System\Configuration\ConfigData;

/**
* configuration used in css
* @package IGK\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
abstract class CssConfiguration{
    public static function ConfigDefaultThemeSelectorFormat(){
        return  igk_configs()->get(ConfigData::CSS_default_theme_selector_format, 'data-theme="%s"');
    }
}