<?php
// @author: C.A.D. BONDJE DOUE
// @file: InlineScriptLoader.php
// @date: 20250401 14:37:42
namespace IGK\System\IO;
use IGK\System\Html\IHtmlGetValue;
use IGKException;
/**
* 
* @package IGK\System\IO
* @author C.A.D. BONDJE DOUE
*/
class InlineScriptLoader implements IHtmlGetValue{

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $file;

    /**
    * .ctr
    * @param string $file
    */
    public function __construct(string $file){
        igk_io_file_exists($file, true) || igk_die('missing file '.igk_sys_lib_filename($file));
        $this->file = $file;
    }
    /**
     * get value on requirement 
     * @param mixed $options 
     * @return string 
     */

    public function getValue($options = null) { 
        return $this->content();
    }

    /**
    * auto generate doc.
    * @return string
    */
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