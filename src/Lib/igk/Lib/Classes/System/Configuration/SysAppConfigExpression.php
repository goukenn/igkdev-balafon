<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SysAppConfigExpression.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Configuration;
use IGK\Helper\StringUtility;
use IGK\Helper\SysUtils;
use IGK\System\Html\IHtmlGetValue;
use Prophecy\Util\StringUtil;
/**
 * retrieve sys expression
 * @package IGK\System\Configuration
 */
class SysAppConfigExpression implements IHtmlGetValue{
    /**
    * Property: expression.
    * @var mixed
    */
    protected $expression;
    /**
     * entry tag
     * @var string
     */
    protected $tag= "app";
    /**
    * .ctr
    * @param string $expression
    */
    public function __construct(string $expression)
    {
        if( empty($expression)){
            die("empty expression not allowed");
        }
        $this->expression = $expression;
    }
    /**
    * Returns Value.
    * @param null|mixed $options
    */
    public function getValue($options = null) { 
       return $this->getStoreValue(); 
    }
    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        if (method_exists($this, $fc = "get".$n)){
            return $this->$fc();
        }
        return null;
    }
    /**
    * get string presentation.
    */
    public function __toString()
    {
        return (string)$this->__get(StringUtility::CamelClassName($this->expression));
    }
    /**
    * Returns Store Value.
    */
    public function getStoreValue(){
        if (!empty($ex = trim($this->expression)))
            return sprintf("{{ %s.%s }}", $this->tag, $ex);
        return null;
    }
    /**
    * Returns Web Site URI.
    */
    public function getWebSiteURI(){
        return igk_io_baseuri();
    }
}