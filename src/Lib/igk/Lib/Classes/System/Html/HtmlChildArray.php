<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlChildArray.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Html;
use ArrayAccess;
use IGK\System\Collections\ArrayList;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

/**
* Html child array.
* @package IGK\System\Html
*/
class HtmlChildArray extends ArrayList implements ArrayAccess{
    use ArrayAccessSelfTrait;

    /**
    * Activate.
    * @param mixed $n
    */
    public function activate($n){
        $this->m_data[$n] = HtmlActiveAttrib::getInstance();
    }

    /**
    * Deactivate.
    * @param mixed $n
    */
    public function deactivate($n){
        unset($this->m_data[$n]);
    }

    /**
    * Returns debug information for var_dump.
    */
    function __debugInfo()
    {
        return ["childCount"=>$this->count()];
    }

    /**
    * Removes.
    * @param mixed $item
    */
    public function remove($item){
        if (false !== ($index = array_search($item, $this->m_data))){
            unset($this->m_data[$index]);
        }
    }

    /**
    * Clears.
    */
    public function clear(){
        $this->m_data = [];
    }
    /**
     * sort childs 
     * @param $callback 
     * @return void 
     */
    public function sort(?callable $callback = null){
        usort($this->m_data, $callback ?? function ($a, $b){
            $ai= $a->getIndex() ?? 0;
            $bi= $b->getIndex() ?? 0;
            return $ai <=> $bi;
        });  
    }

    /**
    * First.
    */
    public function first(){
        if (count($this->m_data)){
            return $this->m_data[0];
        }

    }

    /**
    * Last.
    */
    public function last(){
        if ($c = count($this->m_data)){
            return $this->m_data[$c-1];
        }
    }
}