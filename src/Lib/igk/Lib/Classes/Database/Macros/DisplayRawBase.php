<?php
// @author: C.A.D. BONDJE DOUE
// @file: DisplayRawBase.php
// @date: 20230117 11:00:56
namespace IGK\Database\Macros;


///<summary></summary>
/**
* 
* @package IGK\Database\Macros
*/
abstract class DisplayRawBase{
    
    /**
     * display item
     * @param mixed $item 
     * @return mixed 
     */
    public function display($item){
        $cl = basename(igk_uri(get_class($item)));
        if (method_exists($this, $fc = 'display_'.$cl)){
            return $this->$fc($item);
        }
        return $this->_fallback($item);
    }
    protected function _fallback($item){        
    }
}