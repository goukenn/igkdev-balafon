<?php
// @author: C.A.D. BONDJE DOUE
// @file: IStorage.php
// @date: 20230305 19:17:22
namespace IGK\System\IO\Storage;


///<summary></summary>
/**
* 
* @package IGK\System\IO\Storage
*/
interface IStorage{
    function store(string $file, bool $ovewrite=false): ?string;
    function delete(string $path);
}