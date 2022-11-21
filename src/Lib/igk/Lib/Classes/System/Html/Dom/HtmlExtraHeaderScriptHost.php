<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlExtraHeaderScriptHost.php
// @date: 20221120 12:10:54
namespace IGK\System\Html\Dom;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Dom
*/
final class HtmlExtraHeaderScriptHost extends HtmlNode{
    private $m_list;
    var $tagname = 'igk-extra-header-script';
    public static function Create(array $list){
        if (empty($list))
            return null;
        $g = new self;
        $g->m_list = $list;
        return $g;
    }
    public function getCanRenderTag()
    {
        return false;
    }
   
    public function getRenderedChilds($options = null)
    { 
        return $this->m_childs? 
            array_map([$this, '_init_list'], $this->m_list, array_keys($this->m_list))
            : null;        
    }
    private function _init_list($a, $id=null){
        $n = igk_create_node('script');
        
        $n['src']= igk_io_append_query($a, "v=".IGK_VERSION);
        if (is_numeric($id))
        {
            $id = igk_css_str2class_name(basename($id));            
        }
        $n['id']= $id;
        $ids[$id] = [];
        return $n;
    }
}