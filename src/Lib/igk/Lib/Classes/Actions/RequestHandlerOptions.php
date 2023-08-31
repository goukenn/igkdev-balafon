<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestHandlerOptions.php
// @date: 20230803 20:04:44
namespace IGK\Actions;


///<summary></summary>
/**
* 
* @package IGK\Actions
*/
class RequestHandlerOptions{
    /**
     * method
     * @var string
     */
    var $method = 'GET';

    /**
     * user that initiate the request 
     * @var mixed
     */
    var $user;
    /**
     * is ajx demand
     * @var false
     */
    var $is_ajx = false;
    /**
     * json data for ajx demand - on POST
     * @var mixed
     */
    var $requestData;
}