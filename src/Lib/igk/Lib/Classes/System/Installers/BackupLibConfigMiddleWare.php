<?php

// @author: C.A.D. BONDJE DOUE
// @filename: BackupLibConfigMiddleWare.php
// @date: 20220428 16:57:07
// @desc: 


namespace IGK\System\Installers;


use function igk_resources_gets as __;

/**
 * backup library configuration middelware
 * @package IGK\System\Installers
 */
final class BackupLibConfigMiddleWare extends InstallerActionMiddleWare{
    private $m_config;
    public function getMessage(){
        return __("backup library configuration");
    }
    public function invoke(){
        $service = $this->getServiceInfo();        
        $f = implode("/", [$service->Listener->LibDir, IGK_DATA_FOLDER, "config.xml"]);
        
        if (file_exists($f)){
            $this->m_config = tempnam(sys_get_temp_dir(), "igk");
            copy($f, $this->m_config);
            igk_reg_hook(SuccessMiddleWare::EVENT, function()use($f){  
                $this->getServiceInfo()->Listener->write("restore config info");
                rename($this->m_config, $f);
            });
        }
        $this->next();
    }
    public function abort()
    {
        return parent::abort();
    }
}