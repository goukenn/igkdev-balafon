<?php
// @author: C.A.D. BONDJE DOUE
// @file: DotEnvVarConfiguration.php
// @date: 20260718 13:04:11
namespace IGK\System\IO;

use IGK\System\Regex\Replacement;

/**
* 
* @package IGK\System\IO
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\IO
*/
class DotEnvVarConfiguration{
    /**
     * replacement in use
     * @var ?array
     */
    var $replaceKeys;
    /**
     * 
     * @var mixed
     */
    private static $sm_chain_load =[];
    /**
    * auto generate doc.
    * @var ?string
    */
    private $m_expression;

    /**
     * retrieve keys 
     * @var string
     */
    private $m_key;
    /**
     * configuration host 
     * @var mixed
     */
    private $m_config;
    /**
    * .ctr
    * @param string $expression 
    * @param string $key current key
    * @param array $config configuration host 
    * @return void
    */
    public function __construct(string $expression, string $key, & $config)
    {
        $this->m_expression = $expression;
        $this->m_key = $key;
        $this->m_config = & $config;
    }
    /**
    * auto generate doc.
    * @return string
    */
    public function getExpression(){
        return $this->m_expression;
    }
    /**
    * auto generate doc.
    * @return string
    */
    public function getKey(){
        return $this->m_key;
    }
    private function _regexCallabale(){
        if ($keys = $this->replaceKeys ){
            return sprintf('/\\$(%s)/', implode('|', $keys));
        }
        return '/\\$([a-z_][a-z0-9_]*)/i';
    }
    /**
    * get string presentation.
    * @return string 
    */
    public function __toString()
    { 
        $rp = new Replacement;
        $rpkeys= $this->_regexCallabale();
        $rp->addCallable($rpkeys, function($a){
            $g = $a[1];
            ($g==$this->m_key) && igk_die('invalid expression');
            if (isset(self::$sm_chain_load[$g])) igk_die(sprintf('chain loaded recursion detected at %s', $g ));
            self::$sm_chain_load[$g] = 1;
            $rv = $this->_resolve($g);
            if ($rv instanceof static){
                $rv = ''.$rv;
            }
            unset(self::$sm_chain_load[$g]);
            return $rv;
        });
        return $rp->replace($this->m_expression);
    }
    /**
     * resolve configuration and chain on system key 
     * @param string $g 
     * @return void 
     */
    public function _resolve(string $g){
        $n = igk_getv($this->m_config, $g);
        if (is_null($n) && !key_exists($g, $this->m_config)){
            return DotEnvConfiguration::Get($g);
        }
        return $n;
    }
    public function __debugInfo()
    {
        return [];
    }
}