<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DBMockingCacheData.php
// @date: 20221119 15:16:03
// @desc: help mock data on loading system db cache structure
namespace IGK\System\Caches;
use IGK\Controllers\BaseController;

/**
* Dbcache mocking data.
* @package IGK\System\Caches
*/
class DBCacheMockingData {
    /**
    * Map of table.
    * @var mixed
    */
    var $table;
    /**
    * Property: controller.
    * @var mixed
    */
    var $controller;
    /**
    * Map of table row reference.
    * @var mixed
    */
    var $tableRowReference;
    /**
    * Map of def table name.
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