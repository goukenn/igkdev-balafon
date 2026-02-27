<?php
// @author: C.A.D. BONDJE DOUE
// @file: EntityConfigurationSchema.php
// @date: 20251024 12:03:23
namespace IGK\System\Configuration;



/**
* base entity configuration schema
* @package IGK\System\Core\Configuration
* @author C.A.D. BONDJE DOUE
*/
abstract class EntityConfigurationSchema{
    /**
     * name
     * @var string
     */
    var $name;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $version;

    /**
    * auto generate doc.
    */    var $author;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $license;

    /**
     * repository to GitHub
     * @var ?string
     */
    var $repos;

    /**
     * documentation url
     * @var ?string
     */
    var $url;

    /**
     * general description to display
     * @var mixed
     */
    var $description; 
}