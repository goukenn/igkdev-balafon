<?php
// @author: C.A.D. BONDJE DOUE
// @file: JsonPackageAuthorInfoValidator.php
// @date: 20230330 14:34:41
namespace IGK\System\Npm;
use IGK\System\WinUI\Forms\FormData;
/**
* auto generate doc.
* @package IGK\System\Npm
*/
class JsonPackageAuthorInfoValidator extends FormData{
    /**
    * Name of name.
    * @var mixed
    */
    var $name;
    /**
    * Property: email.
    * @var mixed
    */
    var $email;
    /**
    * Property: url.
    * @var mixed
    */
    var $url;
    /**
    * Returns Not Required.
    * @return ?array
    */
    function getNotRequired(): ?array
    {
        return ['*'];
    }
}