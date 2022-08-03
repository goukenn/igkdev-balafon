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
    protected $expression;
    /**
     * entry tag
     * @var string
     */
    protected $tag= "app";

    public function __construct(string $expression)
    {
        if( empty($expression)){
            die("empty expression not allowed");
        }
        $this->expression = $expression;
    }

    public function getValue($options = null) { 
       return $this->getStoreValue(); 
    }
    public function __get($n){
        if (method_exists($this, $fc = "get".$n)){
            return $this->$fc();
        }
        return null;
    }

    public function __toString()
    {
        return (string)$this->__get(StringUtility::CamelClassName($this->expression));
    }
    public function getStoreValue(){
        if (!empty($ex = trim($this->expression)))
            return sprintf("{{ %s.%s }}", $this->tag, $ex);
        return null;
    } 
    public function getWebSiteURI(){
        return igk_io_baseuri();
    }
}