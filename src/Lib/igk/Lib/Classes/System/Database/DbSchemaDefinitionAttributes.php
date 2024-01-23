<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbSchemaDefinitionAttributes.php
// @date: 20231227 08:45:46
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
class DbSchemaDefinitionAttributes{
    var $author;
    var $version;
    var $createAt;
    var $ControllerName;  
    var $Platform = IGK_PLATEFORM_NAME;
    var $PlatformVersion = IGK_WEBFRAMEWORK;
}