<?php
// @author: C.A.D. BONDJE DOUE
// @file: JsonPackageAuthorInfoValidator.php
// @date: 20230330 14:34:41
namespace IGK\System\Npm;
use IGK\System\WinUI\Forms\FormData;
/**
* 
* @package IGK\System\Npm
*/
class JsonPackageAuthorInfoValidator extends FormData{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $name;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $email;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $url;

    /**
    * auto generate doc.
    * @return ?array
    */
    function getNotRequired(): ?array
    {
        return ['*'];
    }
}