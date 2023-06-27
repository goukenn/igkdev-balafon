<?php
// @author: C.A.D. BONDJE DOUE
// @file: XAFCulture.php
// @date: 20230517 11:09:25
namespace IGK\System\Culture;


///<summary></summary>
/**
* 
* @package IGK\System\Culture
*/
class XAFCulture extends Globalization{
    var $decimalSeparator = '.';
    var $currencyName = 'XAF';
    var $currencySymbol = 'XAF';
    var $format = '%.0f';
    var $symbolPostFix = true;
}