<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConfigurationSchema.php
// @date: 20251023 21:02:01
namespace IGK\System\Controllers\Modules\Configuration;

use IGK\System\Configuration\EntityConfigurationSchema;

/**
* 
* @package IGK\System\Controllers\Modules\Configuration
* @author C.A.D. BONDJE DOUE
*/
class ConfigurationSchema extends EntityConfigurationSchema {
    /**
     * name of the module
     * @var ?string
     */
    var $name;
    /**
     * version of the module 
     * @var ?string
     */
    var $version;
    /**
     * string license type of the module
     * @var mixed
     */
    var $license;
    /**
     * 
     * @var ?array
     */
    var $require;
    /**
     * repository to GitHub
     * @var ?string
     */
    var $repos;
    /**
     * author of the module 
     * @var ?string
     */
    var $author;

    /**
     * array of composer's package definition 
     * @var mixed
     */
    var $composerRequire;

    /**
     * array of node's package definition 
     */
    var $nodeRequire;

    /**
     * 
     * @var mixed
     */
    var $entryNamespace;

    /**
     * represent a type/categorie of the module
     * @var null|'balafon-core-module'|string
     */
    var $type;
}