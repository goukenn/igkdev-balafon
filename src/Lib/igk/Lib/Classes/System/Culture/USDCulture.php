<?php
// @author: C.A.D. BONDJE DOUE
// @file: USDCulture.php
// @date: 20230517 10:59:45
namespace IGK\System\Culture;


///<summary></summary>
/**
* 
* @package IGK\System\Culture
*/
class USDCulture extends Globalization{
    var $decimalSeparator = '.';
    var $currencyName = 'USD';
    var $currencySymbol = '$';
    var $format = '%.2f';
    var $symbolPostFix = false;
}