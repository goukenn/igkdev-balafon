<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleConfiguration.php
// @date: 20251118 12:56:59
namespace IGK\System\Configuration;

/**
* module configuration 
* @package IGK\System\Configuration
* @author C.A.D. BONDJE DOUE
*/
class ModuleConfiguration extends EntityConfigurationSchema{
    /**
     * list of contribution
     * @var ?string[]
     */
    var $contributes;
    /**
     * tags that is in use of module 
     * @var ?string[]
     */
    var $tags;
    /**
     * icon path that will represent the module
     * @var ?string
     */
    var $icon;
    /**
     * entry namespace
     * @var ?string
     */
    var $entry_NS;
    /**
     * auto require the module.
     * - onDemand is the same as false, need to require module manually
     * - true will be require on application start 
     * - 
     * @var null|bool|"onDemand"
     */
    var $autoRequire;
    /**
     * retrieve the configuration value
     * @param string $key 
     * @param mixed $default 
     * @return void 
     */
    public function get(string $key, $default = null){
        return $this->$key ?? $default;
    }
}