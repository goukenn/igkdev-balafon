<?php
// @author: C.A.D. BONDJE DOUE
// @file: IStorageBuilder.php
// @date: 20230305 19:21:02
namespace IGK\System\IO\Storage;


///<summary></summary>
/**
* 
* @package IGK\System\IO\Storage
*/
interface IStorageBuilder{
    function store(string $file, bool $ovewrite=false, ?string $type=null): ?IStorageInfo;
}