<?php
// @author: C.A.D. BONDJE DOUE
// @file: IActionRequestValidator.php
// @date: 20230126 19:30:39
namespace IGK\Actions;


///<summary></summary>
/**
* 
* @package IGK\Actions
*/
interface IActionRequestValidator{    
    /**
     * validate data 
     * @param mixed $data data to validate
     * @param array $mapper mapper array 
     * @param mixed $requestData request data result 
     * @param null|array $error error in case of false
     * @return bool 
     */
    function validate($data, array $mapper, & $requestData = null,  ?array & $error=null): bool;
}