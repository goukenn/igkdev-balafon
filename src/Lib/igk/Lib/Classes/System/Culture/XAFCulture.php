<?php
// @author: C.A.D. BONDJE DOUE
// @file: XAFCulture.php
// @date: 20230517 11:09:25
namespace IGK\System\Culture;

/**
* auto generate doc.
* @package IGK\System\Culture
*/
class XAFCulture extends Globalization{
    /**
    * Property: decimal separator.
    * @var mixed
    */
    var $decimalSeparator = '.';
    /**
    * Name of currency name.
    * @var mixed
    */
    var $currencyName = 'XAF';
    /**
    * Property: currency symbol.
    * @var mixed
    */
    var $currencySymbol = 'XAF';
    /**
    * Property: format.
    * @var mixed
    */
    var $format = '%.0f';
    /**
    * Property: symbol post fix.
    * @var mixed
    */
    var $symbolPostFix = true;
}