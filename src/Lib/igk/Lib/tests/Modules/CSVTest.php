<?php

use IGK\Tests\BaseTestCase;

class CSVTest extends BaseTestCase{
    public function test_csv_date_time(){
        $g = IGKCSVDataAdapter::ToDateTimeStr("Y-m-d", "04/08/1983"); 
        $this->expectOutputString("1983-08-04");
        echo $g;
    }
}