<?php
// author: C.A.D. BONDJE DOUE
// desc: installer utility class helper
namespace IGK\System\Installers;

final class InstallerUtils
{
    private function __construct(){        
    }
    public static function GetEntryPointSource(array $options = null ){
        if ($options===null){
            $options = [];
        }
        $src = file_get_contents(IGK_LIB_DIR."/Inc/core/index.entry_point.pinc");
        foreach([
            "{{ @author }}"=> igk_getv($options,"author", IGK_AUTHOR),
            "{{ @license }}"=> igk_getv($options,"license", "MIT License"), 
            "{{ @date }}"=> date("Y/m/d H:i:s") 

        ] as $k=>$v){
            $src = str_replace($k , $v, $src);
        }
        return $src;
    }
}