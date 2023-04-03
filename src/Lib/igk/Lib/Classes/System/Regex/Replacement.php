<?php
// @author: C.A.D. BONDJE DOUE
// @file: Replacement.php
// @date: 20221206 07:32:52
namespace IGK\System\Regex;

use Closure;
use IGK\Core\Traits\NoDynamicPropertyTrait;

///<summary></summary>
/**
* 
* @package IGK\System\Regex
*/
class Replacement{
    use NoDynamicPropertyTrait;
    /**
     * 
     * @var array<ReplacementObject>
     */
    var $infos = [];
    /**
     * options to store 
     * @var array
     */
    var $options = [];

    /**
     * get number of replacement infos;
     * @return int 
     */
    public function getCount(){
        return count($this->infos);
    }

    /**
     * replace engine
     * @param string $source 
     * @return string 
     */
    public function replace(string $source): string{
        if (!empty($source))
        foreach($this->infos as $v){
            if ($v->type == 'callback'){
                $g = $v->replace;
                $source = preg_replace_callback($v->pattern, $g, $source);
                continue;
            }
            if (preg_match($v->pattern, $source)){
                $source = preg_replace($v->pattern, $v->replace, $source);
            }
        }
        // $error = preg_last_error();
        return $source;
    }
    /**
     * add replacement object
     * @param string $pattern 
     * @param string $replace 
     * @return static 
     */
    public function add(string $pattern, string $replace){
        $rp = new ReplacementObject();
        $rp->pattern = $pattern;
        $rp->replace = $replace;
        $this->infos[] = $rp;
        return $this;
    }
    /**
     * add callable
     * @param string $pattern 
     * @param callable $callback 
     * @return $this 
     */
    public function addCallable(string $pattern, callable $callback){
        $rp = new ReplacementObject();
        $rp->pattern = $pattern;
        $rp->replace = $callback;
        $rp->type = 'callback';
        $this->infos[] = $rp;
        return $this;
    }
    public function clear(){
        $this->infos = [];
    }
    public function __toString()
    {
        return __CLASS__;
    }

    /**
     * replace to regex word
     * @param mixed $s 
     * @return mixed 
     */
    public static function RegexWord($s){
        $s = str_replace("/","\/", $s);
        $s = str_replace(".",'\.', $s);
        return $s;
    }
    /**
     * create a regex expression from string
     * @param string $s string to treat
     * @param string $delimiter default is '/'
     * @param string $op option to add 
     * @return string 
     */
    public static function RegexExpressionFromString(string $s, $delimiter="/", $op=""){
        $s = self::RegexWord($s);
        return $delimiter.$s.$delimiter.$op;
    }
    /**
     * create a not match regex callback.
     * @param mixed $regex 
     * @param mixed $tab 
     * @param int $flag 
     * @return Closure(mixed $s): bool 
     */
    public static function NotMatchRegex($regex, & $tab=null, $flag=0){
        return function($s)use($regex, & $tab, $flag){
            return !preg_match($regex, $s, $tab, $flag);
        };
    }
}