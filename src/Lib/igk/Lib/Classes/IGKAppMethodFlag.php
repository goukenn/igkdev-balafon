<?php
// @author: C.A.D. BONDJE DOUE
// @file: IGKAppMethodFlag.php
// @date: 20230201 07:59:09

/**
* 
* @package IGK
*/
/**
* auto generate doc.
* @package
*/
class IGKAppMethodFlag{
    /**
    * Property: f.
    * @var mixed
    */
    private $m_f;
    /**
    * auto generate doc.
    * @return bool
    */
    public function isEmpty(): bool{
        return empty($this->m_f);
    }
    /**
    * auto generate doc.
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