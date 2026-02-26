<?php
// @author: C.A.D. BONDJE DOUE
// @file: IAssetManager.php
// @date: 20230425 11:21:43
namespace IGK\System\IO;
/**
*asset manager 
* @package IGK\System\IO
*/
interface IAssetManager{

    /**
    * auto generate doc.
    * @param string $file_or_reference
    */
    function addAssets(string $file_or_reference);
}