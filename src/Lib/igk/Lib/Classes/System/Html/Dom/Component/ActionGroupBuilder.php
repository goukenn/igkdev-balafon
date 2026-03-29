<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionGroupBuilder.php
// @date: 20221123 22:41:16
namespace IGK\System\Html\Dom\Component;
/**
* auto generate doc.
* @package IGK\System\Html\Dom\Component
*/
class ActionGroupBuilder{
    /**
    * Property: target.
    * @var mixed
    */
    var $target;
    /**
    * Property: options.
    * @var mixed
    */
    var $options;
    /**
    * Adds Seperator.
    */
    public function addSeperator(){
        return $this->target->span()->setClass('igk-sep');
    }
    /**
    * Input.
    * @param null|mixed $name
    * @param mixed $type
    * @param null|mixed $value
    */
    public function input($name=null, $type='text', $value=null){
        $i = $this->target->input($name, $type, $value);
        $i->setClass('action-item');
        return $i;
    }
    /**
    * Builds.
    * @param array $items
    */
    public function build(array $items){
        while(count($items)>0){
            $key = key($items);
            $i = array_shift($items);
            if (is_numeric($key)){
                // + | '''consider the name in key '
                if (is_string($i)){
                    $key = $i;
                } else {
                    $key = igk_getv($i, 'name');
                }
            }
            if (is_string($i)){
                if ($i == '-'){
                    $this->addSeperator();
                    continue;
                }               
                $i = ['name'=>$i, 'text'=>__($i)];
            }
            $i = ActionGroupItemOptions::ActivateNew($i);           
            switch($i->type){
                case 'button':
                default:
                    $this->input("", 'button', $i->value);
                break;
            }
        }
    }
}