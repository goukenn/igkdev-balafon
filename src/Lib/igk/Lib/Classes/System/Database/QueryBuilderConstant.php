<?php
// @author: C.A.D. BONDJE DOUE
// @filename: QueryBuilderConstant.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Database;

/**
 * define use query builder constant
 * @package IGK\System\Database
 */
abstract class QueryBuilderConstant {
    /**
    * Constant: left join.
    * @var mixed
    */
    const LeftJoin = "LEFT JOIN";
    /**
    * Constant: inner join.
    * @var mixed
    */
    const InnerJoin = "INNER JOIN";
    /**
    * Constant: join.
    * @var mixed
    */
    const Join = "JOIN";
    /**
    * Constant: joins.
    * @var mixed
    */
    const Joins = "Joins";
    /**
    * Constant: group by.
    * @var mixed
    */
    const GroupBy = "GroupBy";
    /**
    * Constant: limit.
    * @var mixed
    */
    const Limit = "Limit";
    /**
    * Constant: distinct.
    * @var mixed
    */
    const Distinct = "Distinct";
    /**
    * Constant: order by.
    * @var mixed
    */
    const OrderBy = "OrderBy";
    /**
    * Constant: columns.
    * @var mixed
    */
    const Columns = "Columns";
}