<?php
// @author: C.A.D. BONDJE DOUE
// @file: ComponentInfo.php
// @date: 20260830 13:38:26
namespace IGK\Core\Components;

use IGK\Helper\Activator;
use IGKEvents;

/**
* represent component information 
* @package IGK\Core\Components
* @author C.A.D. BONDJE DOUE
*/
class ComponentInfo{
    /**
     * 
     * @var ?string
     */
    var $description;
    /**
     * class used by the component 
     * @var ?string
     */
    var $class;


    /**
     * 
     * @return array 
     */
    public static function ListComponentInfo(){
        $f = [];
        $data = json_decode(file_get_contents(IGK_LIB_DIR.'/Data/components/classes.json'), true);
        $data = array_filter($data, function($a){ return !empty($a); });
        foreach($data as $d=>$s){
            $rd = [];
            if (is_string($s)){
                $rd['class'] = $s;
            }else{
                $rd = (array)$s;
            }
            $f[$d] = Activator::CreateNewInstance(static::class, $rd);
        }

        igk_hook(IGKEvents::FILTER_COMPONENT_INFO, ['filter'=>& $f]);
        return $f;
    }
}