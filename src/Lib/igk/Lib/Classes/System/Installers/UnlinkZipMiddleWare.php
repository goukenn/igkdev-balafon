<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BackupLibConfigMiddleWare.php
// @date: 20220428 16:57:07
// @desc: 
namespace IGK\System\Installers;
use IGK\Helper\IO;
use function igk_resources_gets as __;

/**
* auto generate doc.
* @package IGK\System\Installers
*/
final class UnlinkZipMiddleWare extends InstallerActionMiddleWare{

    /**
    * auto generate doc.
    */
    public function invoke(){
        $srv=$this->getServiceInfo()->Listener;
        if ($srv->fromUpload && igk_io_file_exists($srv->CoreZip)){
            $srv->write("unlink corezip");
            @unlink($srv->CoreZip);
        } 
        $this->next();
    }

    /**
    * auto generate doc.
    */
    public function getMessage(){
        return __("unlink zip core");
    }
}