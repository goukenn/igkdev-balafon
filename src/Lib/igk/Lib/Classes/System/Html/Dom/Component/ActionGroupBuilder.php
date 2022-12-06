<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionGroupBuilder.php
// @date: 20221123 22:41:16
namespace IGK\System\Html\Dom\Component;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Dom\Component
*/
class ActionGroupBuilder{
    var $target;

    var $options;

    public function addSeperator(){
        return $this->target->span()->setClass('igk-sep');
    }
    public function input($name=null, $type='text', $value=null){
        $i = $this->target->input($name, $type, $value);
        $i->setClass('action-item');
        return $i;
    }
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