<?php
// @author: C.A.D. BONDJE DOUE
// @file: VersionClass.php
// @date: 20221122 12:50:06
namespace IGK\Projects\Database;

/**
* project database version storage 
* @package IGK\Projects\Database
*/
class VersionClass{
    /**
    * auto generate doc.
    * @var int_primary_auto_index
    */
    var $id;
    /**
    * auto generate doc.
    * @var string_unique(35
    */
    var $version;
    /**
    * auto generate doc.
    * @var string
    */
    var $name;
    /**
    * auto generate doc.
    * @var ?text
    */
    var $author;
    /**
    * auto generate doc.
    * @var ?text
    */
    var $comment;
    /**
    * auto generate doc.
    * @var datetime
    */
    var $createAt;
    /**
    * auto generate doc.
    * @var datetime
    */
    var $updateAt;
}