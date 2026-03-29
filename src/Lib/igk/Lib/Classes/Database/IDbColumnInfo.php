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
    * Returns true if Unsigned.
    * @return bool
    */
    function IsUnsigned():bool;
    /**
    * Returns Is Ref Id.
    * @return bool
    */
    function getIsRefId():bool;
    /**
    * Returns Is Dump Field.
    * @return bool
    */
    function getIsDumpField():bool;
}