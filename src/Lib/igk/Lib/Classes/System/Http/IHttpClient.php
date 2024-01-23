<?php
// @author: C.A.D. BONDJE DOUE
// @file: IHttpClient.php
// @date: 20230913 07:21:29
namespace IGK\System\Http;

use IGK\System\Http\IHttpClientOptions;

///<summary></summary>
/**
* 
* @package IGK\System\Http
* @property bool $followLocation followLocation
* @property ?string $base base server location
*/
interface IHttpClient{
    /**
     * get request info
     * @return null|array 
     */
    function getRequestHeaderResponse(): ?array;
    /**
     * download url 
     * @param string $url 
     * @return mixed 
     */
    function download(string $url, IHttpClientOptions $options);

    /**
     * get data 
     * @param string $url 
     * @return mixed 
     */
    function get(string $url);

    /**
     * post data
     * @param string $url 
     * @param array $data 
     * @return mixed 
     */
    function post(string $url, Array $data=[]);


    /**
     * request with client
     * @param string $url 
     * @return mixed 
     */
    function request(string $url);

    /**
     * get last request status
     * @return int 
     */
    function getStatus():int;
}