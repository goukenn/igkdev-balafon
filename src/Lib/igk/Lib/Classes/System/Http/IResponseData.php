<?php
// @author: C.A.D. BONDJE DOUE
// @file: IResponseData.php
// @date: 20230425 07:43:30
namespace IGK\System\Http;
/**
* 
* @package IGK\System\Http
*/
interface IResponseData{

    /**
    * auto generate doc.
    * @return int
    */
    function getCode() : int;

    /**
    * auto generate doc.
    */
    function getData();
}