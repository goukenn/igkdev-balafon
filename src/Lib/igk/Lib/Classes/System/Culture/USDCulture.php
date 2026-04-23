<?php
// @author: C.A.D. BONDJE DOUE
// @file: USDCulture.php
// @date: 20230517 10:59:45
namespace IGK\System\Culture;

/**
* auto generate doc.
* @package IGK\System\Culture
*/
class USDCulture extends Globalization{
    /**
    * Property: decimal separator.
    * @var mixed
    */
    var $decimalSeparator = '.';
    /**
    * Name of currency name.
    * @var mixed
    */
    var $currencyName = 'USD';
    /**
    * Property: currency symbol.
    * @var mixed
    */
    var $currencySymbol = '$';
    /**
    * Property: format.
    * @var mixed
    */
    var $format = '%.2f';
    /**
    * Property: symbol post fix.
    * @var mixed
    */
    var $symbolPostFix = false;
}