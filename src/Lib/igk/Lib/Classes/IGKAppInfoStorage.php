<?php
 
 /**
  * 
  * @package 
  * @property array $objs object list
  * @property array $ids id list
  * @property array $uris uri list 
  * @property array $srcs src list
  */
 interface IIGKComponentInfo{
 }

/**
 * application info storage
 * @package IGK
 * @property array $controllers;
 * @property array $documents
 * @property IIGKComponentInfo $components
 * @property array $session user's custom session data storage
 * @property array $ctrlParams controller parameters
 * @property bool $config in config mode
 */
class IGKAppInfoStorage extends IGKObject{
    var $data;
    public function __construct(?object $o=null){
        $o = $o ?? (object)[
            "controllers" => [],
            "documents" => [],
            "session"=> [],
            "ctrlParams"=>[],
            "components" => igk_prepare_components_storage()
        ];
        $this->data = $o;        
    }
    public function & getData(){
        return $this->data;
    }
    public function & getSession(){
        $g = & $this->data->session;
        return $g;
    }
    public function __set($n, $v){
        if ($v ===null){
            unset($this->data->$n);
        } else{
            $this->data->$n = $v;
        }
    }
    public function __unset($n){
        unset($this->data->$n); 
    }
    public function & __get($n)
    {
        $g = null;
        if (property_exists($this->data, $n)){
            $g = & $this->data->$n;
        }
        return $g;
    }
    public function __debugInfo()
    {
        return [];
    }
    public function store(string $n, $v){
        $this->$n = $v;
        return $this;
    }
    public function & getRef(string $n){
        if (isset($this->data->$n)){
            $g = & $this->data->$n;
            return $g;
        }
    }
}