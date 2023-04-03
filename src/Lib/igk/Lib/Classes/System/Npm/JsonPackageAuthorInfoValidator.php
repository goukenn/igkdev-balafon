<?php
// @author: C.A.D. BONDJE DOUE
// @file: JsonPackageAuthorInfoValidator.php
// @date: 20230330 14:34:41
namespace IGK\System\Npm;

use IGK\System\WinUI\Forms\FormData;

///<summary></summary>
/**
* 
* @package IGK\System\Npm
*/
class JsonPackageAuthorInfoValidator extends FormData{
    var $name;
    var $email;
    var $url;
    function getNotRequired(): ?array
    {
        return ['*'];
    }

}