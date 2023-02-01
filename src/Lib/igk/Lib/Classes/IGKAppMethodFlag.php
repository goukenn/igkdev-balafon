<?php
// @author: C.A.D. BONDJE DOUE
// @file: IGKAppMethodFlag.php
// @date: 20230201 07:59:09
 


///<summary></summary>
/**
* 
* @package IGK
*/
class IGKAppMethodFlag{
    private $m_f;

    /**
     * 
     * @return bool 
     */
    public function isEmpty(): bool{
        return empty($this->m_f);
    }
    /**
     * 
     * @param mixed $n 
     * @param mixed $def 
     * @return mixed 
     */
    public function getFlag($n, $def=null){
        return igk_getv($this->m_f, $n, $def);
    }
    /**
     * set the flags
     * @param mixed $n 
     * @param mixed $v 
     * @return void 
     */
    public function setFlag($n, $v){
        $this->m_f[$n] = $v;
    }
}