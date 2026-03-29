<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IBalafonInstaller.php
// @date: 20220803 13:48:55
// @desc:
namespace IGK\System\Installers;
/**
* Interface for balafon installer.
* @package IGK\System\Installers
*/
interface IBalafonInstaller{
    /**
     * Performs the update operation.
     *
     * @return mixed
     */
    function update();
    /**
     * Performs the upload operation.
     *
     * @return mixed
     */
    function upload();
}