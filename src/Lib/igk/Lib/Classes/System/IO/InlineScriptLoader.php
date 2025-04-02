<?php
// @author: C.A.D. BONDJE DOUE
// @file: InlineScriptLoader.php
// @date: 20250401 14:37:42
namespace IGK\System\IO;

use IGKException;


///<summary></summary>
/**
* 
* @package IGK\System\IO
* @author C.A.D. BONDJE DOUE
*/
class InlineScriptLoader{
    protected $file;
    public function __construct(string $file){
        file_exists($file) || igk_die('missing file '.igk_sys_lib_filename($file));
        $this->file = $file;
    }
    public function content():string{
        if (igk_environment()->isDev()){
            return file_get_contents($this->file);
        }
        
        $d = igk_js_minify(file_get_contents($this->file));
        // TODO : caching file result 
        $v_hashkey = hash('crc32b', $this->file);
        return $d;
    }
}