<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HashContainer.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\System;

/**
 * use to walk throuw array and check value hash hash
 * @package System
 */
class HashContainer {
    private $m_callback;
    private $m_code;
    public function __construct(string $code, callable $callback)
    {
        $this->m_code = $code;
        $this->m_callback = $callback;
    }
    /**
     * return if contains keys
     * @param mixed $key 
     * @param mixed $tab 
     * @return bool 
     */
    public function contains($key, $tab){
        $k = hash($this->m_code, $key);        
        $ck = $this->m_callback;
        foreach($tab as $a){
            if ($ck($a, $k, $this->m_code)){
                return true;
            }
        } 
        return false;
    }
}