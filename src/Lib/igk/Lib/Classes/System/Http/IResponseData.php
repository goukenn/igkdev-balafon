<?php
// @author: C.A.D. BONDJE DOUE
// @file: IResponseData.php
// @date: 20230425 07:43:30
namespace IGK\System\Http;


///<summary></summary>
/**
* 
* @package IGK\System\Http
*/
interface IResponseData{
    function getCode() : int;
    function getData();
}