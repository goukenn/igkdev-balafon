<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbColumnInfo.php
// @date: 20221104 14:44:30
namespace IGK\Database;


///<summary></summary>
/**
* db column info
* @package IGK\Database
*/
interface IDbColumnInfo extends IDbColumnProperties{
    function IsUnsigned():bool;
    function getIsRefId():bool;
    function getIsDumpField():bool;
}