<?php
// @author: C.A.D. BONDJE DOUE
// @file: CsvFileTest.php
// @date: 20230120 09:22:05
namespace IGK\Tests\System\IO\File;

use IGK\System\IO\File\CsvFile;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
 * 
 * @package IGK\Tests\System\IO\File
 */
class CsvFileTest extends BaseTestCase
{
    public function _testParseData()
    {
        $file = new CsvFile;
        $data = $file->parseData(
            <<<EOF
EOF
        );
        $this->assertEmpty($data);
    }

    public function testParseData2()
    {
        $file = new CsvFile;
        $data = $file->parseData(
            <<<EOF
One, Two, Tree
EOF
        );

        $this->assertEquals(
            json_encode([['One', 'Two', 'Tree']]),
            json_encode($data)
        );
    }
    public function testParseData3()
    {
        $file = new CsvFile;
        $data = $file->parseData(
            <<<EOF
One, 'Two presentation, du jour', Tree
EOF
        );

        $this->assertEquals(
            [['One', 'Two presentation, du jour', 'Tree']],
            $data
        );
    } 
    public function testParseData4()
    {
        $file = new CsvFile;
        $data = $file->parseData(
            <<<EOF
One, 'Two'; Tree
EOF
        );

        $this->assertEquals(
            [['One', "'Two'; Tree"]],
            $data
        );
    } 
    public function testParseData_multiline()
    {
        $file = new CsvFile;
        $data = $file->parseData(
            <<<EOF
Line 1 , One, 'Two'; Tree
Info 1 , Parse
EOF
        ); 
        $this->assertEquals(
            [
                ['Line 1', 'One', "'Two'; Tree"],
                ['Info 1' , 'Parse'],
            ],
            $data
        );
    } 


    public function testParseData_implodeline()
    {
        $file = new CsvFile;
        $file->separator = ',';
  
        $this->assertEquals(
            "One, 'Two'",
            $file->exportLine(['One', "'Two'"])
        );
    } 

    public function test_mapping_data()
    {
        $file = new CsvFile;
        $file->separator = ',';
        $data = $file->parseData("cocacola,500012334,1.5");
        $mapper = [
            "name",
            "codebar",
            "price"
        ];
        $this->assertEquals(
            (object)[
                "name"=>"cocacola",
                "codebar"=>"500012334",
                "price"=>1.5
            ],
            $file->map($data[0], $mapper)
        );
    } 
    public function test_mapping_data_with_callable()
    {
        $file = new CsvFile;
        $file->separator = ',';
        $data = $file->parseData("cocacola,50001A2334,1.5");
        $mapper = [
            "name",
            "codebar"=>function(?string $v, int $i=null){ return strtolower($v);},
            "price"
        ];
        $this->assertEquals(
            (object)[
                "name"=>"cocacola",
                "codebar"=>"50001a2334",
                "price"=>1.5
            ],
            $file->map($data[0], $mapper)
        );
    } 
}
