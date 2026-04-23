<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ServicePropertyTrait.php 
// @date: 20250818 07:44:49
// @desc: service property trait 
namespace IGK\System\Services\Traits;
use IGK\Actions\DispatcherService;
use IGK\Helper\Activator;
use IGK\System\Services\IAppServiceProperty;
use IGKException;
use function igk_resources_gets as __;

/**
 * 
 */
/**
* auto generate doc.
* @package IGK\System\Services\Traits
*/
trait ServicePropertyTrait{
   /**
     * retrieve service configuration properties 
     * @return IAppServiceProperty[] 
     */
    public function getConfigurableProperties(): array { 
        $rf = new \ReflectionClass(static::class);
        $props = $rf->getProperties(\ReflectionProperty::IS_PUBLIC);
        $tab = [];
        foreach($props as $p=>$v){
            if (($l = Activator::CreateNewInstance(IAppServiceProperty::class)) instanceof IAppServiceProperty){
                $t = $v->hasType() ? $v->getType() : null; 
                $l->required = (!$t || !$t->allowsNull())? false : true;
                $l->name = $v->name;
            }
            $tab[$v->name] = $l;
        }
        return $tab;
    }
    /**
    * auto generate doc.
    * @param mixed $configs
    * @return void
    */
    public function validate($configs){
        $props = $this->getConfigurableProperties();
        $conf_props = [];
        $v_validate = true;
        foreach($props as $n=>$p){
            $l = igk_getv($configs, $n);
            if ($p->required && !isset($l)){
                $v_validate = false;
                $n .='*';
            }
            $conf_props[] = $n;
        }
        if (!$v_validate){
            sort($conf_props);
            throw new \IGKException(
                sprintf(__('invalid configuration properties[ %s ]'),
                implode(', ', $conf_props))
            );
        }
    }
    /**
    * auto generate doc.
    * @param mixed $configs
    * @return bool
    */
    public function init($configs=null):bool{
        $this->validate($configs);
        DispatcherService::SetupServiceInstance($this, $configs);
        return true;
    }
}