<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKHostParam.php
// @date: 20220803 13:48:54
// @desc:

/**
* auto generate doc.
*/
class IGKHostParam{
    private $m_data;
    /**
     * 
     * @param mixed $data 
     * @return void 
     */
    public function __construct(object $data)
    {
        if ($data == null){
            igk_die("param not allowed");
        }
        $this->m_data = $data;
    }

    /**
    * auto generate doc.
    * @param mixed $n
    */
    public function __get($n){
        return igk_getv($this->m_data, $n);
    }

    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $value
    */
    public function __set($n, $value){
        if ($value == null){
            unset($this->m_data->$n); 
            return;
        }
        $this->m_data->$n = $value;
    }

    /**
    * auto generate doc.
    */
    public function __toString()
    {
        return __CLASS__;
    }
}