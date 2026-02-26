<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DBMockingCacheData.php
// @date: 20221119 15:16:03
// @desc: help mock data on loading system db cache structure
namespace IGK\System\Caches;
use IGK\Controllers\BaseController;

/**
* auto generate doc.
* @package IGK\System\Caches
*/
class DBCacheMockingData {

    /**
    * auto generate doc.
    * @var mixed
    */
    var $table;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $controller;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $tableRowReference;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $defTableName;

    /**
    * .ctr
    * @param string $table
    * @param null|BaseController $controller
    */
    public function __construct(string $table, ?BaseController $controller = null  ){
        $this->table = $table;
        $this->controller = $controller;
        $this->tableRowReference = [];
    }
}