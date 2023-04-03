<?php
// @author: C.A.D. BONDJE DOUE
// @file: IStorage.php
// @date: 20230328 09:59:00
namespace IGK\System\IO;


///<summary>represent the IStorage interface</summary>
/**
* represent the IStorage interface
* @package IGK\System\IO
*/
interface IStorage{
    /**
     * store path 
     * @param string $path 
     * @param string $content 
     * @return mixed 
     */
    function store(string $path,string $content);
    /**
     * copy system file to storage path
     * @param string $file file to copy
     * @param string $destination_path 
     * @return mixed 
     */
    function copy(string $file, string $destination_path);

    /**
     * unlink path 
     * @param string $path 
     * @return mixed 
     */
    function unlink(string $path);

    /**
     * check if file exists
     * @param string $path 
     * @return bool 
     */
    function exists(string $path):bool;
}