<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKFv.php
// @date: 20220803 13:48:54
// @desc: 



///<summary>represent Internal session flag data</summary>
/**
* represent Internal session flag data
*/
class IGKFv {
    private $_;
	private $_id;
	private $_listener;
    static $sm_def;
    ///<summary></summary>
    /**
    * 
    */
    public function __construct(){
        $this->_=array();
		$this->_listener = null;
		$this->_id = null;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
    public function __get($n){
        return $this->getFlag($n);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){
        $this->setFlag($n, $v);
        return $this;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function __sleep(){
        if(count($this->_) == 0){
            return array();
        }
        else{
            return array('_');
        }
    }
    ///<summary></summary>
    /**
    * 
    */
    public function __wakeup(){
        if($this->_ == null)
            $this->_=array();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function Clear(){
        $this->_=array();
    }
    ///<summary></summary>
    ///<param name="classname"></param>
    ///<param name="tab" ref="true"></param>
    /**
    * 
    * @param mixed $classname
    * @param mixed * $tab
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
    ///<summary>free the flag if test ok</summary>
    /**
    * free the flag if test ok
    */
    public function freeFlag($code, $force=0){
        $g=$this->getFlag($code);
        if($force || ($g == null) || ((is_array($g) && (count($g) == 0)))){
            $this->unsetFlag($code);

			$this->_updateBinding();
        }
    }
    ///<summary></summary>
    ///<param name="classname"></param>
    /**
    * 
    * @param mixed $classname
    */
    public static function Get($classname){
        if(isset(self::$sm_def[$classname])){
            return self::$sm_def[$classname];
        }
        return null;
    }
    ///<summary>get the flag.use explicitly setFlag to store reference data</summary>
    /**
    * get the flag.use explicitly setFlag to store reference data
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
    ///<summary></summary>
    /**
    * 
    */
    public function IsEmpty(){
        return count($this->_) == 0;
    }
    ///<summary></summary>
    ///<param name="code"></param>
    ///<param name="v"></param>
    /**
    * 
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
    ///<summary></summary>
    ///<param name="code"></param>
    /**
    * 
    * @param mixed $code
    */
    public function unsetFlag($code){
        unset($this->_[$code]);
    }
    ///<summary></summary>
    ///<param name="code"></param>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $code
    * @param mixed $v
    */
    public function updateFlag($code, $v){
        $this->setFlag($code, $v);
        $this->freeFlag($code);
    }
}