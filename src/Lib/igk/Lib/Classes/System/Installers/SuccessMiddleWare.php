<?php

// @author: C.A.D. BONDJE DOUE
// @filename: BackupLibConfigMiddleWare.php
// @date: 20220428 16:57:07
// @desc: 


namespace IGK\System\Installers;

use IGK\Helper\IO;

use function igk_resources_gets as __;

///<summary>Represente class: SuccessMiddleWare</summary>
/**
* Represente SuccessMiddleWare class
*/
final class SuccessMiddleWare extends InstallerActionMiddleWare{

    const EVENT = __CLASS__."::Complete";

    ///<summary></summary>
    /**
    * 
    */
    public function getMessage(){
        return __("welldone");
    }
    ///<summary></summary>
    /**
    * 
    */
    public function invoke(){
        $srv=$this->getService();
        if(is_dir($libdir= dirname($this->getService()->LibDir)."/__tempigk")){
            if(!is_dir($vdir=dirname($libdir)."/igk.bck".$srv->Version)){
                $srv->Listener->write(__("Backup previous version"));
                rename($libdir, $vdir);
                igk_zip_folder($vdir.".zip", $vdir, "Lib/igk");
                IO::RmDir($vdir);
                igk_io_w2file(dirname($libdir)."/backup.info", $srv->Version);
            }
            else{
                IO::RmDir($libdir);
            }
        }
        if (function_exists("opcache_reset")){
            opcache_reset();
        }
        $srv->Success=1;
        igk_hook(self::EVENT, [$srv]);
        $this->next();
    }
}
