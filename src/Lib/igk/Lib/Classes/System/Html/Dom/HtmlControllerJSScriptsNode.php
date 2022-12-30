<?php

// @author: C.A.D. BONDJE DOUE
// @filename: HtmlControllerJSScriptsNode.php
// @date: 20220630 16:02:21
// @desc: 

namespace IGK\System\Html\Dom;

use IGK\Helper\ViewHelper;
use IGK\System\IO\StringBuilder;

/**
 * item to auto add controller script node
 * @package IGK\System\Html\Dom
 */
class HtmlControllerJSScriptsNode extends HtmlNode
{

    static $sm_item;
    use HtmlDocumentOnlyTrait;

    public static function getItem()
    {
        if (self::$sm_item === null)
            self::$sm_item = new static;
        return self::$sm_item;
    }
    private function __construct()
    {
        $this->tagname = "igk-controller-js";
    }

    public function render($options = null): ?string
    {
        $ctrl = ViewHelper::BaseController();
        if (is_null($ctrl)) {
            return null;
        }
        $gb = realpath($ctrl->getScriptsDir());
        $is_dev = igk_environment()->isDev();

        $excludedir = igk_sys_js_exclude_dir(); 
        $src = HtmlScriptLoader::LoadScripts(
            [
                [$gb, "ctrl"],
            ],
            $options,
            igk_environment()->isOPS(),
            $excludedir,
            "ctrljs:/" . $ctrl->getName(),
            1
        ); 
        if (!empty($src)) {
            $sb = new StringBuilder();
            $is_dev && $sb->appendLine("<!-- controller_js -->");
            $sb->appendLine($src);
            $is_dev && $sb->appendLine("<!-- end: controller_js -->");
            return $sb . '';
        }
        return null;
    }
}
