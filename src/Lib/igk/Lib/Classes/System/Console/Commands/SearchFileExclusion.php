<?php
// @author: C.A.D. BONDJE DOUE
// @file: SearchFileExclusion.php
// @date: 20260307 12:47:59
namespace IGK\System\Console\Commands;


/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class SearchFileExclusion{
    private $m_patterns;
    var $ignoreCase;
    /**
     * .ctr
     * @return void 
     */
    private function __construct(){
        
    }
    public static function Create(array $patterns, $ignoreCase = false){
        $s = new static;

        $s->m_patterns = $patterns;
        $s->ignoreCase = $ignoreCase;
        return $s;
    }
    public function check(string $haystack):bool{
        $t = $this->m_patterns;
        while(count($t)>0){
            $q = array_shift($t);
            $regex = $this->prepareRegex($q);
            if (preg_match($regex, $haystack)){
                return true;
            }
        }
        return false;
    }
    public function prepareRegex(string $q){
        $q = str_replace("**",'.+', $q);
        $q = str_replace("/*",'\/.+', $q);
        if ($this->ignoreCase){
            $q = '(?i)'.$q;
        }
        $q = str_replace('/','\\/', $q);
        return '/'.$q.'/';
    }
}