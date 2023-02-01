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
    const LeftJoin = "LEFT JOIN";
    const InnerJoin = "INNER JOIN";
    const Join = "JOIN";
    const Joins = "Joins"; 
    const GroupBy = "GroupBy";
    const Limit = "Limit";
    const Distinct = "Distinct";
}