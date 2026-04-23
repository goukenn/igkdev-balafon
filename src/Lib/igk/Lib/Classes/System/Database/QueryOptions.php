<?php
// @author: C.A.D. BONDJE DOUE
// @filename: QueryOptions.php
// @date: 20220803 00:35:51
// @desc: define callback property option
namespace IGK\System\Database;

/**
 * query options
 * @package 
 * @property ?callable @callback
 */
class QueryOptions{
    /**
     * do not use primary key
     * @var bool
     */
    var $noPrimaryKey;
    /**
    * Constant: callback property.
    * @var mixed
    */
    const CallbackProperty = \IGK\Database\DbConstants::CALLBACK_OPTS;
    /**
    * Constant: joins.
    * @var mixed
    */
    const JOINS = 'Joins';
    /**
    * Constant: limit.
    * @var mixed
    */
    const LIMIT = 'Limit';
    /**
    * Constant: order by.
    * @var mixed
    */
    const ORDER_BY = 'OrderBy';
    /**
    * Constant: group by.
    * @var mixed
    */
    const GROUP_BY = 'GroupBy';
}