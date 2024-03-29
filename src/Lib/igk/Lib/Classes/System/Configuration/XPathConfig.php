<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XPathConfig.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Configuration;

//---------------------------------------------------------------------
// XPATH: separated config entry with '/'. [index] if multiple access
//
/**
 * represent the xpath configuration
 */
class XPathConfig{
    private $m_config;
    /**
     * tempory xpath
     * @var false
     */
    var $isTemp = false;
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
    public function __get($n){
        return $this->get($n,null);
    }
}