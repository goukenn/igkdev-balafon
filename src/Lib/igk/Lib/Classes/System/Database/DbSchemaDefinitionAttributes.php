<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbSchemaDefinitionAttributes.php
// @date: 20231227 08:45:46
namespace IGK\System\Database;
/**
* 
* @package IGK\System\Database
*/
class DbSchemaDefinitionAttributes{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $author;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $version;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $createAt;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $ControllerName;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $Platform = IGK_PLATEFORM_NAME;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $PlatformVersion = IGK_WEBFRAMEWORK;
}