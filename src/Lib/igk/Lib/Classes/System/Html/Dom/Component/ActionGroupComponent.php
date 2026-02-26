<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionGroupComponent.php
// @date: 20221123 18:13:51
namespace IGK\System\Html\Dom\Component;
use IGK\Helper\Activator;
use IGK\System\Html\Traits\FieldsDefintionItemTrait;
use IGK\System\Html\Traits\HostableItemTrait;
use IGK\System\Traits\ActivableTrait;
/**
* 
* @package IGK\System\Html\Dom\Component
*/
class ActionGroupComponent extends WebComponent{
    use HostableItemTrait;
    use FieldsDefintionItemTrait;
    use ActivableTrait;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $tagname = 'div';

    /**
    * auto generate doc.
    * @param array $items
    * @param null|mixed $options
    */
    public function fields(array $items, $options = null) { 
        $builder = new ActionGroupBuilder;
        $builder->target = $this;
        $builder->options = $options;
        $builder->build($items);
        return $this;
    }

    /**
    * auto generate doc.
    */
    protected function initialize()
    {
        $this["class"] = "igk-action-group";
    }
}