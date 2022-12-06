<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlAssertNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

use IGK\System\Html\Traits\HostableItemTrait;

/**
 * summary html array looper.
 * Help write view and article template without the php foreach loop
 * @example usage $t->assert(condition)->host(function($n, $a){\
 *                  $n->li()->Content = "Item ".$a;\
 *              });
 */
class HtmlAssertNode extends HtmlItemBase{
    use HostableItemTrait;
    private $condition;
    private $node;  
    private $callback;
    protected $tagname = "igk:assert";
    public function __construct(bool $condition, HtmlItemBase $node){        
        $this->condition = $condition;
        $this->node = $node;
        $this->setFlag("NO_TEMPLATE",1); 
    }   
    public function getCanRenderTag() { return false; }
 
    public function getIsVisible()
    { 
        return $this->condition;
    }
     
    protected function __getRenderingChildren($options =null){
        // before render the childeren . bind callback 
        return parent::__getRenderingChildren($options);
    }
     
}
