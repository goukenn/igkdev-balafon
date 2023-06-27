<?php
// @author: C.A.D. BONDJE DOUE
// @file: IRequestFileHandler.php
// @date: 20230413 14:27:53
namespace IGK\System\Http;


///<summary></summary>
/**
* use to handle file request
* @package IGK\System\Http
*/
interface IRequestFileHandler{
    /**
     * handle request
     * @param string $file 
     * @param bool $render 
     * @return mixed 
     */
    function handleRequest(string $file, bool $render=true);
}