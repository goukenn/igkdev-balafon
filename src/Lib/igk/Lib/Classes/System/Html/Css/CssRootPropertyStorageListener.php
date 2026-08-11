<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssRootPropertyStorageListener.php
// @date: 20241030 16:47:01
namespace IGK\System\Html\Css;

/**
* auto generate doc.
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
            $this->m_roots = $tab;
        }
        else {
            $this->m_roots = array_merge($tab, $this->m_roots);
            ksort($this->m_roots);
        }
    }
    var $m_treat = false;
    /**
     * render root style definition 
     * @return string 
     */
    public function render(){
        $r = & $this->m_roots;
        // if (!$this->m_treat){
        //     $this->m_treat = true;
        //     foreach($r as $k=>$v){
        //         if (preg_match('/\[cl:.+\]/', $v)){
        //             igk_trace();
        //             igk_exit();
        //         }
        //     }
        // }

        return $r ? 
            sprintf(':root{%s}',igk_css_array_key_map_implode($r)) : null; 
    }
}