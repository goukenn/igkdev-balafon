<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigurationData.php
// @date: 20220823 09:33:14
// @desc: base configuration data 

namespace IGK\System\Configuration;

use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGKObject;

/**
 * 
 * @package 
 */
class ConfigurationData extends IGKObject implements ArrayAccess{  
    use ArrayAccessSelfTrait;
    protected  $m_configs;

    protected function _access_OffsetSet($n, $v){
        if (is_null($n)){
            igk_die("set null as array key not allowed");
        }
        if (is_null($v)){
            unset($this->m_configs[$n]);
            return;
        }
        $this->m_configs[$n] = $v;
    }
    protected function _access_OffsetGet($n){
        return igk_getv($this->m_configs,$n);
    }
    protected function _access_offsetExists($n){
        return isset($this->m_configs[$n]);
    }
    protected function _access_OffsetUnset($n){
        unset($this->m_configs[$n]);
    }
}