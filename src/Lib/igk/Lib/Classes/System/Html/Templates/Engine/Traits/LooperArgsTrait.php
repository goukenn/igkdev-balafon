<?php
// @author: C.A.D. BONDJE DOUE
// @file: LooperArgsTrait.php
// @date: 20240123 13:39:26
namespace IGK\System\Html\Templates\Engine\Traits;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Templates\Engine\Traits
* @author C.A.D. BONDJE DOUE
*/
trait LooperArgsTrait{
    public static function TreatArgument($argument){
        $v_args = $argument;
        if (is_null($v_args)){
            return $v_args;
        }
        if (is_numeric($v_args)){
            $max = intval($v_args)-1;
            if ($max < 0){
                igk_die('numeric value must be greather than 1');
            }
            $v_args = range(0, $max);  
        } else if (is_string($v_args) && preg_match("/(?P<min>(-)?\d+)(\s*)?\.\.(\s*)?(?P<max>(-)?\d+)/", $v_args, $tab)){
            $min = $tab['min'];
            $max = $tab['max'];
            if ($max<$min){
                igk_die('numeric range do not respect range max>min');
            }
            $v_args = range($min, $max);  
        }
        return $v_args;
    }
}