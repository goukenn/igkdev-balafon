<?php
// @author: C.A.D. BONDJE DOUE
// @file: IStorage.php
// @date: 20230305 19:17:22
namespace IGK\System\IO\Storage;

/**
* auto generate doc.
* @package IGK\System\IO\Storage
*/
interface IStorage{
    /**
    * Store.
    * @param string $file
    * @param bool $ovewrite
    * @return ?string
    */
    function store(string $file, bool $ovewrite=false): ?string;
    /**
    * Deletes.
    * @param string $path
    */
    function delete(string $path);
}