<?php
// @author: C.A.D. BONDJE DOUE
// @file: IResponseData.php
// @date: 20230425 07:43:30
namespace IGK\System\Http;
/**
* auto generate doc.
* @package IGK\System\Http
*/
interface IResponseData{
    /**
    * Returns Code.
    * @return int
    */
    function getCode() : int;
    /**
    * Returns Data.
    */
    function getData();
}