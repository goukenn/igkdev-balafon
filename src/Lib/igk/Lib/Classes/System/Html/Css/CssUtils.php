<?php

namespace IGK\System\Html\Css;

use IGK\Controllers\BaseController;
use IGKEvents;
use IGKException;

/**
 * utility function 
 * @package IGK\System\Html\Css
 */
abstract class CssUtils
{
    /**
     * 
     * @param null|BaseController $ctrl 
     * @return void 
     * @throws IGKException 
     */
    public static function InitBindingCssFile(BaseController $ctrl, ?string $file = null,  bool $temp=false)
    {
        $f = $file ?? $ctrl->getPrimaryCssFile();
        if (file_exists($f) && !igk_is_ajx_demand()) {
            if (!defined("IGK_FORCSS")) {
                $doc = $ctrl->getCurrentDoc() ?? igk_app()->getDoc();
                igk_css_reg_global_tempfile($f, $doc->getTheme(), $ctrl, $temp);
            } else {
                igk_css_bind_file($ctrl, $f);
            }
            igk_hook(IGKEvents::HOOK_BIND_CTRL_CSS, ["sender" => $ctrl, "type" => "css"]);
        }
    }
}
