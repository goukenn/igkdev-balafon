<?php
// @file: IBalafonApplicationMiddlewareService.php
// @author: C.A.D. BONDJE DOUE
// @copyright: igkdev © 2019
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Services;
use ArrayAccess;
/**
* Represent IBalafonApplicationMiddlewareService interface
*/
interface IBalafonApplicationMiddlewareService extends ArrayAccess{

    /**
    * auto generate doc.
    */    function GetLastMiddleware();
    /**
    * 
    * @param closure callback
    */
    function Run($callback);
    /**
    * 
    * @param mixed instance
    */
    function UseMiddleware($instance);
}