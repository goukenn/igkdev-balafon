<?php

namespace IGK\System\Html\Dom;

/**
 * summary html array looper.
 * Help write view and article template without the php foreach loop
 * @example usage $t->loop([1,2,3])->host(function($n, $a){\
 *                  $n->li()->Content = "Item ".$a;\
 *              });
 */
class HtmlLooperNode extends HtmlItemBase{
    private $args;
    private $node;  
    private $callback;
    protected $tagname = "igk:looper";
    public function __construct($args, $node){        
        $this->args = $args;
        $this->node = $node;
        $this->setFlag("NO_TEMPLATE",1); 
    }   
    public function getCanRenderTag() { return false; }

    public function render($options = null) { return null; }

    public function host(callable $callback){
        foreach($this->args as $k => $c){
            $callback($this->node, $c, $k);
        }
        $this->callback = $callback;
    }   
    public function __getRenderingChildren($options = null){
        $this->host($this->callback);
        return [];        
    }
}
