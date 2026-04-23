<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XPathConfig.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Configuration;

//
/**
 * represent the xpath configuration
 */
class XPathConfig{
    /**
    * Property: config.
    * @var mixed
    */
    private $m_config;
    /**
     * get or set loading tempory. to save configuration
     * @var false
     */
    var $isTemp = false;
    /**
     * init controller 
     * @var bool
     */
    var $initController = true;
    /**
    * .ctr
    * @param mixed $config
    */
    public function __construct($config){
        $this->m_config = $config;
    }
    /**
     * get the config by XPath 
     * 
     */
    public function get($path, $default=null, $strict=0){
        return igk_conf_get($this->m_config, $path, $default, $strict);
    }
    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        return $this->get($n,null);
    }
}