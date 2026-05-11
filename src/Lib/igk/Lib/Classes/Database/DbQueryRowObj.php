<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbQueryRowObj.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Database;
use ArrayAccess;
use Exception;
use IGK\Helper\Utility;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Polyfill\IteratorTrait;
use Iterator;

/**
 * Query row result 
 * @package IGK\Database
 */
class DbQueryRowObj implements ArrayAccess, Iterator, IDbArrayResult{
	use ArrayAccessSelfTrait;
	use IteratorTrait;
    /**
    * Property: rows.
    * @var mixed
    */
    private $m_rows;
    /**
    * Property: it current.
    * @var mixed
    */
    private $it_current;
    /**
    * Property: it keys.
    * @var mixed
    */
    private $it_keys;
    /**
    * Property: it key.
    * @var mixed
    */
    private $it_key;
    /**
    * .ctr
    * @return mixed
    */
    private function __construct(){}
	/**
	 * retrieve column name index
	 * @param int $index 
	 * @return mixed 
	 * @throws Exception 
	 */
    public function column(int $index){
		return igk_getv(array_keys($this->m_rows), $index);
	}
    /**
    * get string presentation.
    */
    public function __toString(){
        return "[".__CLASS__."]";
    }
    /**
    * Used by var_dump() to customize debug output.
    */
    public function __debugInfo()
	{
		return $this->m_rows; 
	}
	/**
	 * get the first value
	 * @return mixed 
	 */
    public function firstValue(){
		$c = $this->m_rows;
		return array_shift($c);
	}
	/**
	 * get the last value
	 * @return mixed 
	 */
    public function lastValue(){
		$c = $this->m_rows;
		return array_pop($c);
	}
    /**
    * To json.
    * @param null|mixed $flag
    */
    public function to_json($flag = null){
        return Utility::To_JSON($this->m_rows, null, $flag);
    }
    /**
    * Creates.
    * @param mixed $tab
    */
    public static function Create($tab){
		if (!$tab || !is_array($tab))
			return null;
		$g = new DbQueryRowObj();
		$g->m_rows = $tab;
		return $g;
	}
    /**
    * To array.
    * @param mixed $filter
    * @return array
    */
    public function to_array($filter=false):array{
		$tab = $this->m_rows;
		if ($filter){
			$tab = array_filter($tab, function($k, $m){
				if (strpos($m, ":") === false){
					return 1;
				}
				return 0;
			},  ARRAY_FILTER_USE_BOTH  );
		}
		return $tab;
	}
    /**
    * Access exists.
    * @param mixed $i
    */
    protected function _access_Exists($i){ 
		return isset($this->m_rows[$i]);
	}
    /**
    * Access offset exists.
    * @param mixed $i
    */
    protected function _access_offsetExists($i){
		return isset($this->m_rows[$i]);
    }
    /**
    * Access offset set.
    * @param mixed $i
    * @param mixed $v
    */
    protected function _access_offsetSet($i, $v){
		$this->m_rows[$i] = $v;
	}
    /**
    * Access offset get.
    * @param mixed $i
    */
    public function _access_OffsetGet($i){
		if ($this->OffsetExists($i)){
			return $this->m_rows[$i];
		}
		return null;
	}
    /**
    * Access offset unset.
    * @param mixed $i
    */
    protected function _access_offsetUnset($i){
		 unset( $this->m_rows[$i]);
	}
    /**
    * check if isset innaccessible property
    * @param mixed $i
    */
    public function __isset($i){ 
		return $this->OffsetExists($i);
	}
    /**
    * .destructor
    * @param mixed $i
    */
    public function __get($i){  
		return $this[$i];
	}
    /**
    * destructor
    * @param mixed $i
    * @param mixed $v
    */
    public function __set($i,$v){
		$this[$i] = $v;
	}
    /**
    * unset innacessible property
    * @param mixed $n
    */
    public function __unset($n){
        $this->OffsetUnset($n);
    }
    /**
    * Iterator current.
    */
    public function _iterator_current (){
		return $this->it_current;
	}
    /**
    * Iterator key.
    */
    public function _iterator_key (){
		return $this->it_keys[$this->it_key];
	}
    /**
    * Iterator next.
    */
    public function _iterator_next (){
		$this->it_key++;
		if (isset($this->it_keys[$this->it_key])){
			$s =  $this->it_keys[$this->it_key];
			$this->it_current = $this[$s];
		}else
			$this->it_current = null;
	}
    /**
    * Iterator rewind.
    */
    public function _iterator_rewind (){
		$this->it_keys = array_keys($this->m_rows);
		$this->it_key = 0;
		$s =  $this->it_keys[$this->it_key];
		$this->it_current = $this[$s];
	}
    /**
    * Iterator valid.
    */
    public function _iterator_valid (){
		return $this->it_key < count($this->it_keys);
	}
	/**
	 * check column exists
	 * @param mixed $name 
	 * @return bool 
	 */
    public function columnExists($name):bool{
		return key_exists($name, $this->m_rows);
	}
    /**
    * Returns count of.
    * @return int
    */
    public function count():int{
		return count($this->m_rows);
	} 
}