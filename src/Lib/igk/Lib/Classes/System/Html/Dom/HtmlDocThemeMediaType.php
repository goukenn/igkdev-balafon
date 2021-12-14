<?php
// @file: HtmlDocThemeMediaType.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;

use IGKObject;

final class HtmlDocThemeMediaType extends IGKObject{
    const CTN_LG_MEDIA=self::LG_MEDIA + self::CTN_OFFSET;
    const CTN_OFFSET=10;
    const CTN_SM_MEDIA=self::SM_MEDIA + self::CTN_OFFSET;
    const CTN_XLG_MEDIA=self::XLG_MEDIA + self::CTN_OFFSET;
    const CTN_XSM_MEDIA=self::XSM_MEDIA + self::CTN_OFFSET;
    const CTN_XXLG_MEDIA=self::XXLG_MEDIA + self::CTN_OFFSET;
    const GT_LG_MEDIA=self::GT_OFFSET + 0x3;
    const GT_OFFSET=0xA0;
    const GT_SM_MEDIA=self::GT_OFFSET + 0x2;
    const GT_XLG_MEDIA=self::GT_OFFSET + 0x4;
    const GT_XSM_MEDIA=self::GT_OFFSET + 0x1;
    const LG_MEDIA=2;
    const SM_MEDIA=1;
    const XLG_MEDIA=3;
    const XSM_MEDIA=0;
    const XXLG_MEDIA=4;
}
