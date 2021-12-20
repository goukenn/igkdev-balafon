<?php


class IGKHostParam{
    private $m_data;

    public function __construct($data)
    {
        $this->m_data  = $data;
    }
    public function __get($n){
        return igk_getv($this->m_data, $n);
    }
    public function __set($n, $value){
        if ($value ==null){
            unset($this->m_data, $value);
        }
        $this->m_data->$n = $value;
    }
    public function __toString()
    {
        return __CLASS__;
    }
}