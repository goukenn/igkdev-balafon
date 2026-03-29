<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKRawDataBinding.php
// @date: 20220803 13:48:54
// @desc:
/**
* Igkraw data binding.
*/
class IGKRawDataBinding implements ArrayAccess {
    use IGK\System\Polyfill\ArrayAccessSelfTrait;
    /**
    * Property: data.
    * @var mixed
    */
    private $m_data;
    /**
     * Constructor.
     */
    private function __construct(){}
    /**
     * Checks whether the given offset exists in the bound data.
     * @param mixed $offset The offset to check.
     * @return bool True if the offset exists, false otherwise.
     */
    public function offsetExists (  $offset ) :bool{
        if (is_object($this->m_data)){
            return property_exists($this->m_data, $offset);
        }
        return array_key_exists($offset, $this->m_data);
    }
    /**
     * Returns the value at the given offset via the magic getter.
     * @param mixed $offset The offset to retrieve.
     * @return mixed The value at the specified offset.
     */
    public function _access_offsetGet (  $offset ) {
        return $this->__get($offset);
    }
    /**
     * Sets the value at the given offset via the magic setter.
     * @param mixed $offset The offset to set.
     * @param mixed $value The value to assign.
     */
    protected function _access_offsetSet ($offset ,$value ) {
        $this->__set($offset, $value);
    }
    /**
     * Unsets the value at the given offset in the bound data.
     * @param mixed $offset The offset to unset.
     */
    protected function _access_offsetUnset($offset) {
        if (is_object($this->m_data)){
            unset($this->m_data->$offset);
            return;
        }
        unset($this->m_data[$offset]);
    }
    /**
     * Creates a new IGKRawDataBinding instance wrapping the given row data.
     * @param array|object $row The data row to bind; must be an array or object.
     * @return static|null A new binding instance, or null if the input is invalid.
     */
    public static function Create($row){
        if (($row == null)|| ((is_array($row)==false) &&  (is_object($row)==false))){
            return null;
        }
        $o = new self();
        $o->m_data = $row;
        return $o;
    }
    /**
     * Returns the value of a named property from the bound data.
     * @param string $n The property name to retrieve.
     * @return mixed The value associated with the given name.
     */
    public function __get($n){
        if (igk_environment()->isDev() && !$this->offsetExists($n)){
            igk_die(__FILE__.":".__LINE__." : offset \"$n\" not present");
        }
        return igk_getv($this->m_data, $n);
    }
    /**
     * Sets a named value in the bound data array.
     * @param string $n The key name to set.
     * @param mixed $v The value to assign.
     */
    public function __set($n, $v){
        $this->m_data[$n] = $v;
    }
    /**
     * Returns a string representation of this binding instance.
     * @return string A bracketed class name string.
     */
    public function __toString(){
        return "[".__CLASS__."]";
    }
}