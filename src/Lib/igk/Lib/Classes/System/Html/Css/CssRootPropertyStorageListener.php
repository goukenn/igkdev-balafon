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
     * 
     * @var mixed
     */
    var $selector;
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
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    private $m_treat = false;
    /**
     * render root style definition 
     * @return ?string 
     */
    public function render(?string $themename=null): ?string{
        $r = & $this->m_roots;
        $skey = null;
        if ($themename){
            $skey = CssUtils::ThemeKey($themename). ' ';
        } else {
            if ($this->selector){
                $skey = trim($this->selector);
            }
        }
       

        return $r ? 
            sprintf($skey.':root{%s}',igk_css_array_key_map_implode($r)) : null; 
    }
    public function clear(){
        $this->m_roots = [];

    }
}