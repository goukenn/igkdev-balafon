<?php
// @author: C.A.D. BONDJE DOUE
// @file: IControllerRequestInfo.php
// @date: 20251211 07:44:36
namespace IGK\System\Controllers;


/**
* 
* @package IGK\System\Controllers
* @author C.A.D. BONDJE DOUE
* @property ?string $method default is GET
* @property ?string $request
* @property ?array  $args in case of non GET verb
* @property ?bool  $isAjx is in ajax context
*/
interface IControllerRequestInfo{
}