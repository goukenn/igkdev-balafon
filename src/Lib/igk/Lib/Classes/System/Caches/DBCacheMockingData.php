<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DBMockingCacheData.php
// @date: 20221119 15:16:03
// @desc: help mock data on loading system db cache structure

namespace IGK\System\Caches;

use IGK\Controllers\BaseController;

class DBCacheMockingData {
    var $table;
    var $controller;
    var $tableRowReference;
    var $defTableName;
    public function __construct(string $table, ?BaseController $controller = null  ){
        $this->table = $table;
        $this->controller = $controller;
        $this->tableRowReference = [];
    }
}