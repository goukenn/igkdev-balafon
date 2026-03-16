<?php
// @author: C.A.D. BONDJE DOUE
// @file: SearchFileExclusion.php
// @date: 20260307 12:47:59
namespace IGK\System\Console\Commands;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class SearchFileExclusion{
    private $m_patterns;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $ignoreCase;
    /**
     * .ctr
     * @return void 
     */
    private function __construct(){
        
    }

    /**
    * auto generate doc.
    * @param array $patterns
    * @param mixed $ignoreCase
    * @return
    */
    public static function Create(array $patterns, $ignoreCase = false){
        $s = new static;

        $s->m_patterns = $patterns;
        $s->ignoreCase = $ignoreCase;
        return $s;
    }

    /**
    * auto generate doc.
    * @param string $haystack
    * @return bool
    */
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

    /**
    * auto generate doc.
    * @param string $q
    * @return
    */
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