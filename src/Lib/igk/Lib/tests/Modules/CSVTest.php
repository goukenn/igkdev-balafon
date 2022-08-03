<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CSVTest.php
// @date: 20220803 13:48:54
// @desc: 


use IGK\Tests\BaseTestCase;

class CSVTest extends BaseTestCase{
    public function test_csv_date_time(){
        $g = IGKCSVDataAdapter::ToDateTimeStr("Y-m-d", "04/08/1983"); 
        $this->expectOutputString("1983-08-04");
        echo $g;
    }
}