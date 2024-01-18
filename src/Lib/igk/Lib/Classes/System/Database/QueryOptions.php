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

    const CallbackProperty = \IGK\Database\DbConstants::CALLBACK_OPTS;
    const JOINS = 'Joins';
    const LIMIT = 'Limit';
    const ORDER_BY = 'OrderBy';
    const GROUP_BY = 'GroupBy';
}