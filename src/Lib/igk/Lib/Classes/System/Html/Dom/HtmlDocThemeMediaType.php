<?php
// @file: HtmlDocThemeMediaType.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;
use IGKObject;

/**
* Html doc theme media type.
* @package IGK\System\Html\Dom
*/
final class HtmlDocThemeMediaType extends IGKObject{
    /**
    * Constant: ctn lg media.
    * @var mixed
    */
    const CTN_LG_MEDIA=self::LG_MEDIA + self::CTN_OFFSET;
    /**
    * Constant: ctn offset.
    * @var mixed
    */
    const CTN_OFFSET=10;
    /**
    * Constant: ctn sm media.
    * @var mixed
    */
    const CTN_SM_MEDIA=self::SM_MEDIA + self::CTN_OFFSET;
    /**
    * Constant: ctn xlg media.
    * @var mixed
    */
    const CTN_XLG_MEDIA=self::XLG_MEDIA + self::CTN_OFFSET;
    /**
    * Constant: ctn xsm media.
    * @var mixed
    */
    const CTN_XSM_MEDIA=self::XSM_MEDIA + self::CTN_OFFSET;
    /**
    * Constant: ctn xxlg media.
    * @var mixed
    */
    const CTN_XXLG_MEDIA=self::XXLG_MEDIA + self::CTN_OFFSET;
    /**
    * Constant: gt lg media.
    * @var mixed
    */
    const GT_LG_MEDIA=self::GT_OFFSET + 0x3;
    /**
    * Constant: gt offset.
    * @var mixed
    */
    const GT_OFFSET=0xA0;
    /**
    * Constant: gt sm media.
    * @var mixed
    */
    const GT_SM_MEDIA=self::GT_OFFSET + 0x2;
    /**
    * Constant: gt xlg media.
    * @var mixed
    */
    const GT_XLG_MEDIA=self::GT_OFFSET + 0x4;
    /**
    * Constant: gt xsm media.
    * @var mixed
    */
    const GT_XSM_MEDIA=self::GT_OFFSET + 0x1;
    /**
    * Constant: lg media.
    * @var mixed
    */
    const LG_MEDIA=2;
    /**
    * Constant: sm media.
    * @var mixed
    */
    const SM_MEDIA=1;
    /**
    * Constant: xlg media.
    * @var mixed
    */
    const XLG_MEDIA=3;
    /**
    * Constant: xsm media.
    * @var mixed
    */
    const XSM_MEDIA=0;
    /**
    * Constant: xxlg media.
    * @var mixed
    */
    const XXLG_MEDIA=4;
}