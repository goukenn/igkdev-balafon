<?php
// @author: C.A.D. BONDJE DOUE
// @file: IStorageBuilder.php
// @date: 20230305 19:21:02
namespace IGK\System\IO\Storage;
/**
* auto generate doc.
* @package IGK\System\IO\Storage
*/
interface IStorageBuilder{
    /**
    * Store.
    * @param string $file
    * @param bool $ovewrite
    * @param null|string $type
    * @return ?IStorageInfo
    */
    function store(string $file, bool $ovewrite=false, ?string $type=null): ?IStorageInfo;
}