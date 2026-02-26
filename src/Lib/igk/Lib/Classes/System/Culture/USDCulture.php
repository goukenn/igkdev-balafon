<?php
// @author: C.A.D. BONDJE DOUE
// @file: USDCulture.php
// @date: 20230517 10:59:45
namespace IGK\System\Culture;
/**
* 
* @package IGK\System\Culture
*/
class USDCulture extends Globalization{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $decimalSeparator = '.';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $currencyName = 'USD';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $currencySymbol = '$';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $format = '%.2f';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $symbolPostFix = false;
}