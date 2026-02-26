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
    * auto generate doc.
    * @var mixed
    */
    const LeftJoin = "LEFT JOIN";

    /**
    * auto generate doc.
    * @var mixed
    */
    const InnerJoin = "INNER JOIN";

    /**
    * auto generate doc.
    * @var mixed
    */
    const Join = "JOIN";

    /**
    * auto generate doc.
    * @var mixed
    */
    const Joins = "Joins";

    /**
    * auto generate doc.
    * @var mixed
    */
    const GroupBy = "GroupBy";

    /**
    * auto generate doc.
    * @var mixed
    */
    const Limit = "Limit";

    /**
    * auto generate doc.
    * @var mixed
    */
    const Distinct = "Distinct";

    /**
    * auto generate doc.
    * @var mixed
    */
    const OrderBy = "OrderBy";

    /**
    * auto generate doc.
    * @var mixed
    */
    const Columns = "Columns";
}