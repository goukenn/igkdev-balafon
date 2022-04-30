<?php

namespace IGK\System\Html\Dom;

class HtmlCenterBoxNode extends HtmlNode
{
    private $content_node;
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
    protected function __getRenderingChildren($options = null)
    {
        return [
            $this->content_node
        ];
    }
    protected function _Add($n, $force=false){
        return $this->content_node->add($n);
    }
}
