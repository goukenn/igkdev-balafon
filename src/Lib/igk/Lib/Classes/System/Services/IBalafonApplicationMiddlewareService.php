<?php
// @file: IIGKBalafonApplicationMiddlewareService.php
// @author: C.A.D. BONDJE DOUE
// @copyright: igkdev © 2019
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Services;

use ArrayAccess;

///<summary>Represente interface: IIGKBalafonApplicationMiddlewareService</summary>
/**
* Represent IIGKBalafonApplicationMiddlewareService interface
*/
interface IBalafonApplicationMiddlewareService extends ArrayAccess{
    ///<summary></summary>
    /**
    * 
    */
    function GetLastMiddleware();
    ///<summary></summary>
    ///<param name="callback"></param>
    /**
    * 
    * @param closure callback
    */
    function Run($callback);
    ///<summary></summary>
    ///<param name="instance"></param>
    /**
    * 
    * @param mixed instance
    */
    function UseMiddleware($instance);
}
