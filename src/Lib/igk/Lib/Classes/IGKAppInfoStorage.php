<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKAppInfoStorage.php
// @date: 20220803 13:48:54
// @desc: 

 
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
 * application session info storage. help reduce the coast of session file 
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
        $o = $o ?? $this->createSessionInfoStorage();
        $this->data = $o;        
    }
   /**
    * 
    * @return object 
    */
    protected function createSessionInfoStorage(){
        return (object)[
            "controllers" => [],
            "documents" => [],
            "session"=> [],
            "ctrlParams"=>[],
            "components" => igk_prepare_components_storage()
        ];
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