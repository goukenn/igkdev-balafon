<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormDataJsonTrait.php
// @date: 20230707 17:50:06
namespace IGK\FormData\Traits;

use IGK\Actions\IActionRequestValidator;
use IGK\Helper\Activator;
use IGK\System\Http\IContentSecurityProvider;
use IGK\System\Http\Request;

///<summary></summary>
/**
* 
* @package IGK\FormData\Traits
*/
trait FormDataJsonTrait{
 /**
     * create instance from json request data
     * @param mixed $data 
     * @return static 
     */
    public static function FromJSonRequestData($data, IContentSecurityProvider $request, IActionRequestValidator $validator, & $errors=null){
        return Activator::CreateNewInstanceWithValidation(static::class, $data, $request, $validator, $errors);
    }
}