<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ViewsTest.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK\Tests\System\WinUI;

use IGK\System\WinUI\Pagination;
use IGK\Tests\BaseTestCase;

class ViewsTest extends BaseTestCase{


    public function test_pagination_query() { 
        $q = "";
        $gramm = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER)->getGrammar();
        $_REQUEST["p"] = 3;
        $pan = new Pagination(10, 50);
        $q = $gramm->createSelectQuery("sample", null, [
            "Limit"=>$pan->getLimit()
        ]);
        $this->assertEquals(
            'SELECT * FROM `sample` Limit 20,10;',
            $q
        );
    }

    public function test_pagination_list() { 
        $q = "";
        $gramm = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER)->getGrammar();
        $_REQUEST["p"] = 6;
        $pan = new Pagination(10, 50);        
        
        $this->assertIsObject( 
            $pan->list()
        );
    }
}