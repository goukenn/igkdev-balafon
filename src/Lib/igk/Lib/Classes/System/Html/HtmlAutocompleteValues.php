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

    /**
    * auto generate doc.
    * @var mixed
    */
    const OFF = 'off';

    /**
    * auto generate doc.
    * @var mixed
    */
    const CURRENT_PASSWORD = 'current-password';

    /**
    * auto generate doc.
    * @var mixed
    */
    const USERNAME = 'username';

    /**
    * auto generate doc.
    * @var mixed
    */
    const NOPE = 'nope';

    /**
    * auto generate doc.
    * @var mixed
    */
    const NEW_PASSWORD = 'new-password';

    /**
    * auto generate doc.
    * @var mixed
    */
    const OTP = 'one-time-code';

    /**
    * auto generate doc.
    * @var mixed
    */
    const COUNTRY = 'country';

    /**
    * auto generate doc.
    * @var mixed
    */
    const COUNTRY_NAME = 'country-name';

    /**
    * auto generate doc.
    * @var mixed
    */
    const COUNTRY_CODE = 'country-code';

    /**
    * auto generate doc.
    * @var mixed
    */
    const URL = 'url';

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHOTO = 'photo';

    /**
    * auto generate doc.
    * @var mixed
    */
    const CARD_NUMBER = 'cc-number';
}