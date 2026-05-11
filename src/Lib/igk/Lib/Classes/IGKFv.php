<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKFv.php
// @date: 20220803 13:48:54
// @desc: 

/**
* represent Internal session flag data
*/
class IGKFv {
    /**
    * Property: .
    * @var mixed
    */
    private $_;
    /**
    * Identifier: id.
    * @var mixed
    */
    private $_id;
    /**
    * Listener: listener.
    * @var mixed
    */
    private $_listener;
    /**
    * Property: def.
    * @var mixed
    */
    static $sm_def;
    /**
    * auto generate doc.
    */
    public function __construct(){
        $this->_=array();
		$this->_listener = null;
		$this->_id = null;
    }
    /**
    * auto generate doc.
    * @param mixed $n
    */
    public function __get($n){
        return $this->getFlag($n);
    }
    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){
        $this->setFlag($n, $v);
        return $this;
    }
    /**
    * auto generate doc.
    */
    public function __sleep(){
        if(count($this->_) == 0){
            return array();
        }
        else{
            return array('_');
        }
    }
    /**
    * auto generate doc.
    */
    public function __wakeup(){
        if($this->_ == null)
            $this->_=array();
    }
    /**
    * auto generate doc.
    */
    public function Clear(){
        $this->_=array();
    }
    /**
    * auto generate doc.
    * @param mixed $classname
    * @param mixed & $tab
    * @param mixed * $listener update listener
    */
    public static function Create($classname, & $tab, $listener = null){
        if(isset(self::$sm_def[$classname])){
            igk_die("- already created for {$classname} -");
        }
        $o=new IGKFv();
        $o->_=& $tab;
		$o->_id = $classname;
		$o->_listener = $listener;
        self::$sm_def[$classname]=$o;
        return $o;
    }
    /**
    * free the flag if test ok
    * @param mixed $code
    * @param mixed $force
    */
    public function freeFlag($code, $force=0){
        $g=$this->getFlag($code);
        if($force || ($g == null) || ((is_array($g) && (count($g) == 0)))){
            $this->unsetFlag($code);
			$this->_updateBinding();
        }
    }
    /**
    * auto generate doc.
    * @param mixed $classname
    */
    public static function Get($classname){
        if(isset(self::$sm_def[$classname])){
            return self::$sm_def[$classname];
        }
        return null;
    }
    /**
    * get the flag.use explicitly setFlag to store reference data
    * @param mixed $code
    * @param mixed & $default
    * @param mixed $register
    */
    public function & getFlag($code, & $default=null, $register=0){
        $g=null;
        if(isset($this->_[$code]))
            $g=& $this->_[$code];
        else{
            if($register && ($default !== null)){
                $g=& $default;
                $this->_[$code]=& $g;
            }
            else{
                return $default;
            }
        }
        return $g;
    }
    /**
    * auto generate doc.
    */
    public function IsEmpty(){
        return count($this->_) == 0;
    }
    /**
    * auto generate doc.
    * @param mixed $code
    * @param mixed $v
    */
    public function setFlag($code, $v){
        if(func_num_args() < 2){
            igk_die("Argument count");
        }
        if($v === null)
            $this->unsetFlag($code);
        else{
            if(is_array($v))
                $this->_[$code]=& $v;
            else
                $this->_[$code]=$v;
        }
		$this->_updateBinding();
    }
    /**
    * auto generate doc.
    * @return mixed
    */
    private function _updateBinding(){
		if ($this->_listener){
			$c = [];
			$c[] = & $this->_;
			call_user_func_array($this->_listener, $c);
		}else {
		if ($classname = $this->_id){
			if (!empty($this->_)){
				igk_app()->session->registerControllerParams($classname, $this->_);
			}else {
				igk_app()->session->unregisterControllerParams($classname, $this->_);
			}
		}
		}
	}
    /**
    * auto generate doc.
    * @param mixed $code
    */
    public function unsetFlag($code){
        unset($this->_[$code]);
    }
    /**
    * auto generate doc.
    * @param mixed $code
    * @param mixed $v
    */
    public function updateFlag($code, $v){
        $this->setFlag($code, $v);
        $this->freeFlag($code);
    }
}