<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigUtils.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Configuration;
/**
* Config utils.
* @package IGK\System\Configuration
*/
class ConfigUtils{
    /**
     * load configuration utility
     * @param string $file 
     * @param mixed $data 
     * @param bool $autocontext 
     * @return void 
     */
    public static function LoadData(string $file, & $data, $autocontext=true, bool $contextual=false){      
        $data = @include($file); 
        if ($autocontext && ($ctx = igk_environment()->context()) != "web"){
            $dir = dirname($file);            
            $ext = igk_io_path_ext($file);
            if (igk_io_file_exists($fc = $dir ."/".implode(".",[igk_io_basenamewithoutext($file), $ctx, $ext]), true)){
                $cdata = include($fc);
                $data = array_replace($data, $cdata); 
                if ($contextual){
                    igk_environment()->set('env://config/contextual', $cdata);
                }
            }
        }  
    }
}