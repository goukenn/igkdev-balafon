<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlCenterBoxNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;

/**
* Html center box node.
* @package IGK\System\Html\Dom
*/
class HtmlCenterBoxNode extends HtmlNode
{
    /**
    * Property: content node.
    * @var mixed
    */
    private $content_node;
    /**
    * .ctr
    * @param null|mixed $content
    */
    public function __construct($content = null)
    {
        parent::__construct("div");
        $this["class"] = "igk-centerbox";
        if ($content) {
            if (is_string($content)) {
                $this->content = $content;
            } else if (is_callable($content)) {
                $this->host($content);
            } else {
                $this->content = igk_ob_get($content);
            }
        }
        $this->content_node = new HtmlNode("div");
        $this->content_node["class"] = "content";
        parent::_Add($this->content_node);
    }
    /**
    * Get rendering children.
    * @param null|mixed $options
    */
    protected function _getRenderingChildren($options = null)
    {
        return [
            $this->content_node
        ];
    }
    /**
    * Add.
    * @param mixed $n
    * @param mixed $force
    * @return bool
    */
    protected function _add($n, $force=false):bool {
        return $this->content_node->_add($n, $force);
    }
}