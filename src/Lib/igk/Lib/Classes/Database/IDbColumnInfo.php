<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbColumnInfo.php
// @date: 20221104 14:44:30
namespace IGK\Database;
/**
* db column info
* @package IGK\Database
*/
interface IDbColumnInfo extends IDbColumnProperties{

    /**
    * auto generate doc.
    * @return bool
    */
    function IsUnsigned():bool;

    /**
    * auto generate doc.
    * @return bool
    */
    function getIsRefId():bool;

    /**
    * auto generate doc.
    * @return bool
    */
    function getIsDumpField():bool;
}