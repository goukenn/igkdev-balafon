<?php
// @author: C.A.D. BONDJE DOUE
// @file: FileMatcher.php
// @date: 20230307 22:33:16
namespace IGK\System\Regex;
/**
 * helper used the match callable for directory
 * @package IGK\System\Regex
 */
class FileMatcher
{
    /**
    * Constant: not match.
    * @var mixed
    */
    const NOT_MATCH = 1;
    /**
    * Type of type.
    * @var mixed
    */
    var $type = 0;
    /**
    * Path to base dir.
    * @var mixed
    */
    var $base_dir;
    /**
    * Property: regex.
    * @var mixed
    */
    var $regex;
    /**
    * Property: flags.
    * @var mixed
    */
    var $flags = 0;
    /**
    * Property: tab.
    * @var mixed
    */
    var $tab;
    /**
    * Property: init.
    * @var mixed
    */
    private $m_init;
    /**
     * parse matcher
     * @return null|string 
     */
    public function parseMachter():?string{
        if (is_null($this->regex)){
            return null;
        }
        $t = $this->regex;
        $t = str_replace("**","\/?([^\/]+\/?)(\/)?", $t);
        $t = str_replace("*","[^\/]+", $t);
        return $t;
    }
    /**
    * auto generate doc.
    * @param null|string $file
    * @return int|bool
    */
    public function match(?string $file)
    {
        if (!$this->m_init){
            //parse matcher 
            $this->regex = $this->parseMachter();
            $this->m_init = true;
        }
        if (($this->base_dir) && (strpos($file, $this->base_dir) === 0))
            $file = substr($file, strlen($this->base_dir));
        $r = preg_match($this->regex, $file, $this->tab, $this->flags);
        if ($this->type == self::NOT_MATCH) {
            return !$r;
        }
        return $r;
    }
    /**
    * Called when an object is used as a function.
    * @param null|string $file
    */
    public function __invoke(?string $file)
    {
        return $this->match($file);
    }
}