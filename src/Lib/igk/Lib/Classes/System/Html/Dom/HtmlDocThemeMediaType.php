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
* auto generate doc.
* @package IGK\System\Html\Dom
*/
final class HtmlDocThemeMediaType extends IGKObject{

    /**
    * auto generate doc.
    * @var mixed
    */
    const CTN_LG_MEDIA=self::LG_MEDIA + self::CTN_OFFSET;

    /**
    * auto generate doc.
    * @var mixed
    */
    const CTN_OFFSET=10;

    /**
    * auto generate doc.
    * @var mixed
    */
    const CTN_SM_MEDIA=self::SM_MEDIA + self::CTN_OFFSET;

    /**
    * auto generate doc.
    * @var mixed
    */
    const CTN_XLG_MEDIA=self::XLG_MEDIA + self::CTN_OFFSET;

    /**
    * auto generate doc.
    * @var mixed
    */
    const CTN_XSM_MEDIA=self::XSM_MEDIA + self::CTN_OFFSET;

    /**
    * auto generate doc.
    * @var mixed
    */
    const CTN_XXLG_MEDIA=self::XXLG_MEDIA + self::CTN_OFFSET;

    /**
    * auto generate doc.
    * @var mixed
    */
    const GT_LG_MEDIA=self::GT_OFFSET + 0x3;

    /**
    * auto generate doc.
    * @var mixed
    */
    const GT_OFFSET=0xA0;

    /**
    * auto generate doc.
    * @var mixed
    */
    const GT_SM_MEDIA=self::GT_OFFSET + 0x2;

    /**
    * auto generate doc.
    * @var mixed
    */
    const GT_XLG_MEDIA=self::GT_OFFSET + 0x4;

    /**
    * auto generate doc.
    * @var mixed
    */
    const GT_XSM_MEDIA=self::GT_OFFSET + 0x1;

    /**
    * auto generate doc.
    * @var mixed
    */
    const LG_MEDIA=2;

    /**
    * auto generate doc.
    * @var mixed
    */
    const SM_MEDIA=1;

    /**
    * auto generate doc.
    * @var mixed
    */
    const XLG_MEDIA=3;

    /**
    * auto generate doc.
    * @var mixed
    */
    const XSM_MEDIA=0;

    /**
    * auto generate doc.
    * @var mixed
    */
    const XXLG_MEDIA=4;
}