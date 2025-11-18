<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ServicePropertyTrait.php 
// @date: 20250818 07:44:49
// @desc: service property trait 
namespace IGK\System\Services\Traits;

use IGK\Helper\Activator;
use IGK\System\Services\IAppServiceProperty;

use function igk_resources_gets as __;

trait ServicePropertyTrait{
   /**
     * 
     * @return IAppServiceProperty[] 
     */
    public function getConfigurableProperties(): array { 
        $rf = new \ReflectionClass(static::class);
        $props = $rf->getProperties(\ReflectionProperty::IS_PUBLIC);
        $tab = [];
        foreach($props as $p=>$v){
            if (($l = Activator::CreateNewInstance(IAppServiceProperty::class)) instanceof IAppServiceProperty){
                // $t = $v->hasType() ? $v->getType() : null;
                $l->required = false;
                $l->name = $v->name;
            }
            $tab[$v->name] = $l;
        }
        return $tab;
    }
    public function validate($configs){

        $props = $this->getConfigurableProperties();
        $conf_props = [];
        $v_validate = true;

        foreach($props as $n=>$p){
            $l = igk_getv($configs, $n);
            if ($p->required && !isset($l)){
                $v_validate = false;
            }
            $conf_props[] = $n;
        }
        if (!$v_validate){
            throw new \IGKException(
                sprintf(__('invalid configuration properties, %s'),
                implode(', ', $conf_props))
            );
        }
    }
}
