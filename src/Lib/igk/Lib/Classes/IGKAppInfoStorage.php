<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKAppInfoStorage.php
// @date: 20220803 13:48:54
// @desc: 
use IGK\System\ApplicationSessionStorage;
/**
 * application session info storage. help reduce the coast of session file 
 * @package IGK
 * @property array $controllers;
 * @property array $documents
 * @property \IGK\IComponentInfo $components
 * @property array $session user's custom session data storage
 * @property array $ctrlParams controller parameters
 * @property bool $config in config mode
 */
class IGKAppInfoStorage extends IGKObject{
    /**
    * Property: data.
    * @var mixed
    */
    var $data;
    /**
    * .ctr
    * @param null|object $o
    */
    public function __construct(?object $o=null){
        $o = $o ?? $this->createSessionInfoStorage();
        $this->data = $o;        
    }
   /**
    * application storage 
    * @return object 
    */
    protected function createSessionInfoStorage(){
        $src = new ApplicationSessionStorage;
        $src->components = igk_prepare_components_storage();
        return (object)(array)$src;
        // return (object)[
        //     "controllers" => [],
        //     "documents" => [],
        //     "session"=> [],
        //     "ctrlParams"=>[],
        //     "components" => igk_prepare_components_storage()
        // ];
    }
    /**
    * Returns Data.
    */
    public function & getData(){
        return $this->data;
    }
    /**
    * Returns Session.
    */
    public function & getSession(){
        $g = & $this->data->session;
        return $g;
    }
    /**
    * destructor
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){
        if ($v ===null){
            unset($this->data->$n);
        } else{
            $this->data->$n = $v;
        }
    }
    /**
    * unset innacessible property
    * @param mixed $n
    */
    public function __unset($n){
        unset($this->data->$n); 
    }
    /**
    * .destructor
    * @param mixed $n
    */
    public function & __get($n)
    {
        $g = null;
        if (property_exists($this->data, $n)){
            $g = & $this->data->$n;
        }
        return $g;
    }
    /**
    * Used by var_dump() to customize debug output.
    */
    public function __debugInfo()
    {
        return [];
    }
    /**
    * Store.
    * @param string $n
    * @param mixed $v
    */
    public function store(string $n, $v){
        $this->$n = $v;
        return $this;
    }
    /**
    * Returns Ref.
    * @param string $n
    */
    public function & getRef(string $n){
        if (isset($this->data->$n)){
            $g = & $this->data->$n;
            return $g;
        }
    }
}