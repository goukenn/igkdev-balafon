<?php
// @author: C.A.D. BONDJE DOUE
// @file: IAssetBuilder.php
// @date: 20230720 10:37:20
namespace IGK\System\Assets;


///<summary></summary>
/**
* asset builder interface 
* @package IGK\System\Assets
*/
interface IAssetBuilder{
    function build($module, string $asset_dir);
}