<?php
// @author: C.A.D. BONDJE DOUE
// @filename: QueryOptions.php
// @date: 20220803 00:35:51
// @desc: define callback property option

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
}