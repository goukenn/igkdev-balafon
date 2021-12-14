<?php

///<summary>
class IGKAppSetting{
    private $info;

    public function __construct($info){
        if (!is_object($info)) die("info not valid");
        $this->info = $info;
    }
    public function __get($n){
        if (isset($this->info->$n)){
            return $this->info->$n;
        }
        return null;
    }
    public function __set($n, $v){
        if ($v === null){
            unset($this->info->$n);
        } else {
            $this->info->$n = $v;
        }
        return $this;
    }
}