<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlExtraHeaderScriptHost.php
// @date: 20221120 12:10:54
namespace IGK\System\Html\Dom;
/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
final class HtmlExtraHeaderScriptHost extends HtmlNode{
    /**
    * Collection of list.
    * @var mixed
    */
    private $m_list;
    /**
    * Name of tagname.
    * @var mixed
    */
    var $tagname = 'igk-extra-header-script';
    /**
    * Creates.
    * @param array $list
    */
    public static function Create(array $list){
        if (empty($list))
            return null;
        $g = new self;
        $g->m_list = $list;
        return $g;
    }
    /**
    * Returns Can Render Tag.
    */
    public function getCanRenderTag()
    {
        return false;
    }
    /**
    * Returns Rendered Childs.
    * @param null|mixed $options
    */
    public function getRenderedChilds($options = null)
    { 
        return $this->m_childs? 
            array_map([$this, '_init_list'], $this->m_list, array_keys($this->m_list))
            : null;        
    }
    /**
    * auto generate doc.
    * @param mixed $a
    * @param null|mixed $id
    * @return
    */
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