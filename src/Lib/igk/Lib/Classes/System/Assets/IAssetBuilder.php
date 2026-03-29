<?php
// @author: C.A.D. BONDJE DOUE
// @file: IAssetBuilder.php
// @date: 20230720 10:37:20
namespace IGK\System\Assets;
/**
* asset builder interface 
* @package IGK\System\Assets
*/
interface IAssetBuilder{
    /**
    * Builds.
    * @param mixed $module
    * @param string $asset_dir
    */
    function build($module, string $asset_dir);
}