<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssRootPropertyStorageListener.php
// @date: 20241030 16:47:01
namespace IGK\System\Html\Css;
/**
* 
* @package IGK\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssRootPropertyStorageListener implements ICssStoreRootListener{

    /**
    * Property: roots.
    * @var mixed
    */
    private $m_roots;
    /**
     * root listener 
     * @param array $tab 
     * @return void 
     */

    public function store(array $tab){
        if (is_null($this->m_roots)){
            $this->m_roots =  $tab;
        }
        else {
            $this->m_roots = array_merge($tab, $this->m_roots);
            ksort($this->m_roots);
        }
    }
    /**
     * render root 
     * @return string 
     */

    public function render(){
        return $this->m_roots ? 
            sprintf(':root{%s}',igk_css_array_key_map_implode($this->m_roots)) : null; 
    }
}