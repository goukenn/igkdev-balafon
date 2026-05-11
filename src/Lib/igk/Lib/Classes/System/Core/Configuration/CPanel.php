<?php
// @author: C.A.D. BONDJE DOUE
// @file: CPanel.php
// @date: 20260509 16:53:01
namespace IGK\System\Core\Configuration;

use IGKEvents;
use IGKHtmlDoc;
/**
* auto generate doc.
* @package IGK\System\Core\Configuration
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Core\Configuration
*/
class CPanel{
    const CPANEL_HOOKS = IGKEvents::CPANEL_HOOKS;
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const HOOK_HANDLE_URI = self::CPANEL_HOOKS.'/filter/handle_uri';
    /**
    * auto generate doc.
    * @param IGKHtmlDoc $doc
    * @return void
    */
    public static function SetupDocument(IGKHtmlDoc $doc){
        $meta = $doc->getMetas();
        $meta->addMeta('robots', 'noindex, nofollow');
    }
}