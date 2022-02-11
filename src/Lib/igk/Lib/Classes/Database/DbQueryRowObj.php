<?php
namespace IGK\Database;

use ArrayAccess;
use IGK\Helper\Utility;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Polyfill\IteratorTrait;
use Iterator;

class DbQueryRowObj implements ArrayAccess, Iterator{
	use ArrayAccessSelfTrait;
	use IteratorTrait;
	private $m_rows;
	private $it_current;
	private $it_keys;
	private $it_key;
    private function __construct(){}
    public function __toString(){
        return "[".__CLASS__."]";
    }
	public function __debugInfo()
	{
		return $this->m_rows; //["m_rows"=>$this->m_rows];
	}
    public function to_json(){
        return Utility::To_JSON($this->m_rows, null);
    }
 
	public static function Create($tab){
		if (!$tab || !is_array($tab))
			return null;
		$g = new DbQueryRowObj();
		$g->m_rows = $tab;
		return $g;
	}
	public function to_array($filter=false){
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
	protected function _access_Exists($i){ 
		return isset($this->m_rows[$i]);
	}
	protected function _access_offsetExists($i){
		return isset($this->m_rows[$i]);
    }
	protected function _access_offsetSet($i, $v){
		$this->m_rows[$i] = $v;
	}
	public function OffsetGet($i){
		if ($this->OffsetExists($i)){
			return $this->m_rows[$i];
		}
		return null;
	}
	protected function _access_offsetUnset($i){
		 unset( $this->m_rows[$i]);
	}

	public function __isset($i){ 
		return $this->OffsetExists($i);
	}
	public function __get($i){ 
		return $this[$i];
	}
	public function __set($i,$v){
		$this[$i] = $v;
	}
    public function __unset($n){
        $this->OffsetUnset($n);
    }

	public function _iterator_current (){
		return $this->it_current;
	}
	public function _iterator_key (){
		return $this->it_keys[$this->it_key];
	}
	public function _iterator_next (){
		$this->it_key++;
		if (isset($this->it_keys[$this->it_key])){
			$s =  $this->it_keys[$this->it_key];
			$this->it_current = $this[$s];
		}else
			$this->it_current = null;
	}
	public function _iterator_rewind (){
		$this->it_keys = array_keys($this->m_rows);
		$this->it_key = 0;
		$s =  $this->it_keys[$this->it_key];
		$this->it_current = $this[$s];
	}
	public function _iterator_valid (){
		return $this->it_key < count($this->it_keys);
	}
   
}
