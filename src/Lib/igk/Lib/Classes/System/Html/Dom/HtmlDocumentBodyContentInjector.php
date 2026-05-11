<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlDocumentBodyContentInjector.php
// @date: 20250404 20:38:56
namespace IGK\System\Html\Dom;

/**
* used to inject some script at different state of the document - depend on list
* @package IGK\System\Html\Dom
* @author C.A.D. BONDJE DOUE
*/
class HtmlDocumentBodyContentInjector{
    /**
    * Collection of list.
    * @var mixed
    */
    private $m_list;
    /**
     * contain 
     * @param string $key 
     * @return bool 
     */
    public function contains(string $key){
        return $this->m_list && key_exists($key, $this->m_list);
    }
    /**
    * auto generate doc.
    * @param string $id
    * @param mixed $callback
    * @return void
    */
    public function register(string $id, $callback){
        if (is_null($this->m_list)){
            $this->m_list = [];
        }
        $this->m_list[$id] = $callback;
    }
    /**
    * Clears.
    */
    public function clear(){
        $this->m_list = [];
    }
    /**
    * Returns Items.
    */
    public function getItems(){
        return $this->m_list; 
    }
}