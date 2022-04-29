<?php

// @author: C.A.D. BONDJE DOUE
// @filename: BackupLibConfigMiddleWare.php
// @date: 20220428 16:57:07
// @desc: 


namespace IGK\System\Installers;

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
        $f = implode("/", [IGK_LIB_DIR, IGK_DATA_FOLDER, "config.xml"]);
        // igk_debug_wln("invoke : ", $f);
        if (file_exists($f)){
            $this->m_config = tempnam(sys_get_temp_dir(), "igk");
            copy($f, $this->m_config);
            // igk_debug_wln("the pass : ", file_get_contents($f),  
            // "config ==== ", 
            // file_get_contents($this->m_config), 
            // $this->m_config);
            igk_reg_hook(SuccessMiddleWare::EVENT, function()use($f){                
                rename($this->m_config, $f);
            });
        }
        $this->next();
    }
    public function abort()
    {
        parent::abort();
    }
}