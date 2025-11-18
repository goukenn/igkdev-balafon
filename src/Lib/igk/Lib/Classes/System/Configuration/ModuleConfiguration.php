<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleConfiguration.php
// @date: 20251118 12:56:59
namespace IGK\System\Configuration;


/**
* 
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
}