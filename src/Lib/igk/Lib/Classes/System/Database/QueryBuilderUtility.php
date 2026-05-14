<?php
// @author: C.A.D. BONDJE DOUE
// @file: QueryBuilderUtility.php
// @date: 20260514 08:05:27
namespace IGK\System\Database;


/**
* 
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
abstract class QueryBuilderUtility{
    /**
     * help left join
     * @param mixed $condition 
     * @return array 
     */
    public static function LeftJoin($condition): array
    {
        return ["type" => QueryBuilderConstant::LeftJoin, $condition];
    }

      /**
    * Inner join.
    * @param mixed $condition
    */
    public static function InnerJoin($condition): array
    {
        return ["type" => QueryBuilderConstant::InnerJoin, $condition];
    }
}