<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKIterator.php
// @date: 20220803 13:48:54
// @desc: 


///<summary>used to iterate thru an array</summary>
/**
* used to iterate thru an array
*/
final class IGKIterator extends IGKObject implements ArrayAccess, Iterator, Countable {
    use \IGK\System\Polyfill\IteratorTrait; 
    use \IGK\System\Polyfill\ArrayAccessSelfTrait;
    private $it_index;
    private $it_vtab;
    private $m_count;
    private $m_index;
    private $m_it_key;
    private $m_target;
    private $m_viewCount;
    ///<summary></summary>
    ///<param name="ob"></param>
    /**
    * 
    * @param mixed $ob
    */
    public function __construct($ob){
        $this->m_target= $ob;
        $this->m_count=igk_count($ob);
        $this->m_index=0;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function count():int{
        return igk_count($this->m_target);
    }
    ///<summary></summary>
    /**
    * @return mixed data
    */
    function _iterator_current(){
        return $this->m_target[$this->m_it_key];
    }
    ///<summary></summary>
    /**
    *  @return mixed data
    */
    function _iterator_key(){
        return $this->m_it_key;
    }
    ///<summary></summary>
    /**
    * 
    */
    function _iterator_next():void{
        $this->it_index++;
        if($this->it_index < $this->m_count){
            $this->m_it_key=$this->it_vtab[$this->it_index];
        }
    }
    
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    protected function _access_offsetExists($key):bool{
        return isset($this->m_target[$key]);
    }
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    protected function _access_offsetGet(mixed $key):mixed{
        if(isset($this->m_target[$key]))
            return $this->m_target[$key];
    }
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="value"></param>
    /**
    * 
    * @param mixed $key
    * @param mixed $value
    */
    protected function _access_offsetSet($key, $value):void{}
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    protected function _access_offsetUnset($key):void{}
    ///<summary></summary>
    /**
    * 
    */
    function _iterator_rewind():void{
        $this->it_vtab=array_keys($this->m_target);
        $this->it_index=$this->m_index;
        $c=count($this->it_vtab);
        if(($c > 0) && ($this->m_index<=$c)){
            $this->m_it_key=$this->it_vtab[$this->m_index];
        }
        else{
            $this->m_it_key=null;
        }
    }
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    public function setrewindStart($i){
        $this->m_index=$i;
    }
    ///<summary></summary>
    ///<param name="index"></param>
    ///<param name="count" default="null"></param>
    /**
    * 
    * @param mixed $index
    * @param mixed $count the default value is null
    */
    public function Shift($index, $count=null){
        $this->m_index=$index;
        if($count && is_numeric($count)){
            $this->m_count=min($index + $count, igk_count($this->m_target));
        }
    }
    ///<summary></summary>
    /**
    * 
    */
    function _iterator_valid(){
        $v=($this->it_index>=0) && ($this->it_index < $this->m_count);
        return $v;
    }
}