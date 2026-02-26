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
    * auto generate doc.
    * @var mixed
    */
    const CallbackProperty = \IGK\Database\DbConstants::CALLBACK_OPTS;

    /**
    * auto generate doc.
    * @var mixed
    */
    const JOINS = 'Joins';

    /**
    * auto generate doc.
    * @var mixed
    */
    const LIMIT = 'Limit';

    /**
    * auto generate doc.
    * @var mixed
    */
    const ORDER_BY = 'OrderBy';

    /**
    * auto generate doc.
    * @var mixed
    */
    const GROUP_BY = 'GroupBy';
}