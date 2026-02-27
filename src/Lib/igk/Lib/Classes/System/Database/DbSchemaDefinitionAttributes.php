<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbSchemaDefinitionAttributes.php
// @date: 20231227 08:45:46
namespace IGK\System\Database;

/**
* auto generate doc.
* @package IGK\System\Database
*/
class DbSchemaDefinitionAttributes{

    /**
    * Property: author.
    * @var mixed
    */
    var $author;

    /**
    * Property: version.
    * @var mixed
    */
    var $version;

    /**
    * Property: create at.
    * @var mixed
    */
    var $createAt;

    /**
    * Name of controller name.
    * @var mixed
    */
    var $ControllerName;

    /**
    * Property: platform.
    * @var mixed
    */
    var $Platform = IGK_PLATEFORM_NAME;

    /**
    * Property: platform version.
    * @var mixed
    */
    var $PlatformVersion = IGK_WEBFRAMEWORK;
}