<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbWhereQueryCondition.php
// @date: 20241013 15:04:39
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
interface IDbWhereQueryCondition{
    /**
     * retreive condition info
     * @return array cond, columns
     */
    function getConditionInfo():array;
}