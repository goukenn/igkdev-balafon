<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlAutocompleteValues.php
// @date: 20250604 05:17:12
namespace IGK\System\Html;


/**
* 
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE
* @documentation https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Attributes/autocomplete#values
*/
abstract class HtmlAutocompleteValues{
    const OFF = 'off';
    const CURRENT_PASSWORD = 'current-password';
    const USERNAME = 'username';
    const NOPE = 'nope';
    const NEW_PASSWORD = 'new-password';
    const OTP = 'one-time-code';
    const COUNTRY = 'country';
    const COUNTRY_NAME = 'country-name';
    const COUNTRY_CODE = 'country-code';
    const URL = 'url';
    const PHOTO = 'photo';
    const CARD_NUMBER = 'cc-number';
}