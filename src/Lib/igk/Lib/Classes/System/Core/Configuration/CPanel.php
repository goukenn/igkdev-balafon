<?php
// @author: C.A.D. BONDJE DOUE
// @file: CPanel.php
// @date: 20260509 16:53:01
namespace IGK\System\Core\Configuration;

use IGKEvents;
use IGKHtmlDoc;

/**
* 
* @package IGK\System\Core\Configuration
* @author C.A.D. BONDJE DOUE
*/
class CPanel{
    const CPANEL_HOOKS = IGKEvents::CPANEL_HOOKS;
    const HOOK_HANDLE_URI = self::CPANEL_HOOKS.'/filter/handle_uri';

    public static function SetupDocument(IGKHtmlDoc $doc){
        $meta = $doc->getMetas();
        $meta->addMeta('robots', 'noindex, nofollow');
    }
}