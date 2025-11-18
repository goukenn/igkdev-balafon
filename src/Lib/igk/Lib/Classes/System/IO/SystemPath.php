<?php
// @author: C.A.D. BONDJE DOUE
// @file: SystemPath.php
// @date: 20250718 18:46:04
namespace IGK\System\IO;

use Exception;
use IGKResourceUriResolver;

/**
* 
* @package IGK\System\IO
* @author C.A.D. BONDJE DOUE
*/
class SystemPath{
    var $path;
    var $options;
    var $query;
    private function __construct(){
    }
    /**
     * 
     * @param string $path 
     * @return static 
     * @throws Exception 
     */
    public static function Parse(string $path){
        $p = parse_url($path);
        $s = new static;
        $s->query = igk_getv($p, 'query');
        list($path, $options) = igk_extract(explode(';', $p['path'], 2), '0|1');
        $s->path = $path;
        $s->options = $options;
        return $s;
    }
    /**
     * check if path exists
     * @return bool 
     * @throws Exception 
     */
    public function exists(){
        return igk_io_file_exists($this->path, true);
    }
    /**
     * get resolve 
     * @return ?string 
     */
    public function resolve(){
        if ($c = IGKResourceUriResolver::getInstance()->resolve($this->path)){
            // if ($this->options){
            //     $c.=';'.$this->options;
            // }
            if ($this->query){
                $c.='?'.$this->query;
            }
            return $c;
        }
    }
}