<?php
// @author: C.A.D. BONDJE DOUE
// @file: IActionRequestValidator.php
// @date: 20230126 19:30:39
namespace IGK\Actions;

use IGK\System\Data\IDataValidator;
use IGK\System\Http\Request;

///<summary></summary>
/**
* 
* @package IGK\Actions
*/
interface IActionRequestValidator extends IDataValidator{    
    /**
     * validate data 
     * @param mixed $data data to validate
     * @param array $mapper mapper array 
     * @param mixed $requestData request data result 
     * @param null|array $error error in case of false
     * @return bool 
     */
    function validate($data, array $mapper,  ?array $defaultValues, ?array $not_required, & $requestData = null,  ?array & $error=null): bool;

    /**
     * validate request json 
     * @param IGK\Actions\Request $request 
     * @param string $formdata_class 
     * @param null|array $error 
     * @return bool|mixed 
     */
    function validateJSon(Request $request, string $formdata_class, ?array & $error=null);
}