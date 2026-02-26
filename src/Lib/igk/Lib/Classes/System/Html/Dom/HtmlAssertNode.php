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

    /**
    * auto generate doc.
    * @var mixed
    */
    private $condition;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $node;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $callback;

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $tagname = "igk:assert";

    /**
    * .ctr
    * @param bool $condition
    * @param HtmlItemBase $node
    */
    public function __construct(bool $condition, HtmlItemBase $node){        
        $this->condition = $condition;
        $this->node = $node;
        $this->setFlag("NO_TEMPLATE",1); 
    }

    /**
    * auto generate doc.
    */
    public function getCanRenderTag() { return false; }

    /**
    * auto generate doc.
    */
    public function getIsVisible()
    { 
        return $this->condition;
    }

    /**
    * auto generate doc.
    * @param null|mixed $options
    */
    protected function _getRenderingChildren($options =null){
        // before render the childeren . bind callback 
        return parent::_getRenderingChildren($options);
    }
}