<?php
// @author: C.A.D. BONDJE DOUE
// @file: VersionClass.php
// @date: 20221122 12:50:06
namespace IGK\Projects\Database;


///<summary></summary>
/**
* 
* @package IGK\Projects\Database
*/
class VersionClass{
    /**
     * 
     * @var int_primary_auto_index
     */
    var $id;
     /**
     * 
     * @var string_unique(35)
     */
    var $version;

    /**
     * 
     * @var string
     */
    var $name;
     /**
     * 
     * @var ?text
     */
    var $author;
     /**
     * 
     * @var ?text
     */
    var $comment;
     /**
     * 
     * @var datetime
     */
    var $createAt;
     /**
     * 
     * @var datetime
     */
    var $updateAt;
    
}
