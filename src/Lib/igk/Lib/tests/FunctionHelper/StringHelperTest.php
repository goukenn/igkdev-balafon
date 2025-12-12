<?php
// @author: C.A.D. BONDJE DOUE
// @filename: StringHelperTest.php
// @date: 20231018 08:05:44
// @desc: test function 
// 

namespace IGK\Tests\FunctionHelper;

use IGK\System\IO\CSV\Helper\CSVHelper;
use IGK\Tests\BaseTestCase;
use IGKCSVDataAdapter;

class StringHelperTest extends BaseTestCase
{
    public function test_igk_str_transform_linefeed()
    {
        $v = 'information,\ndata';
        $b = igk_str_transform_linefeed($v);
        $this->assertEquals("information,\ndata", $b);
    }
    public function test_igk_addslashes()
    {
        $v = "information,\ndata";
        $b =  igk_str_replace_assoc_array(["\n" => '\n'], $v);
        $this->assertEquals('information,\ndata', $b);
    }

    public function test_read_csv_json()
    {
        $v = 'information,"{\"data\":5,\"m\":8}"';
        $b = IGKCSVDataAdapter::LoadString($v);
        $this->assertTrue(count($b[0]) == 2);
        $c = stripslashes($b[0][1]);
        $r = json_decode($c);
        $this->assertEquals((object)['data' => 5, "m" => 8], $r);


        $v = 'information,"{"data":5,"m":8}"';
        $b = IGKCSVDataAdapter::LoadString($v);
        $this->assertTrue(count($b[0]) == 2);
        $r = json_decode($c);
        $this->assertEquals((object)['data' => 5, "m" => 8], $r);
    }
    public function test_read_csv_json_hello()
    {
        $v = 'information,"{\"data\":\"hello\\\\nm\"}"';
        $b = IGKCSVDataAdapter::LoadString($v);
        $this->assertTrue(count($b[0]) == 2);
        //$c = stripslashes($b[0][1]);
        $r = json_decode($b[0][1]);
        $this->assertEquals((object)['data' => "hello\nm"], $r);


        $v = 'information,"{"data":"hello\\\\nm"}"';
        $b = IGKCSVDataAdapter::LoadString($v);
        $this->assertTrue(count($b[0]) == 2);
        $c = $b[0][1];
        $r = json_decode($c);
        $this->assertEquals((object)['data' => "hello\nm"], $r);
    }
    public function test_read_csv_serie_data()
    {
        // $m = serialize([1=>"title"]);
        // $v = 'information,"'.$m.'"';
        // $b = IGKCSVDataAdapter::LoadString($v);
        // $this->assertTrue(count($b[0])==2);
        // $r = unserialize($b[0][1]); 
        // $this->assertEquals([1=>"title"], $r);


        // $m = serialize([1=>"title,data"]);
        // $v = 'information,"'.$m.'"';
        // $b = IGKCSVDataAdapter::LoadString($v);
        // $this->assertTrue(count($b[0])==2);
        // $r = unserialize($b[0][1]); 
        // $this->assertEquals([1=>"title,data"], $r);


        $m = serialize([1 => "title\ndata"]);
        $v = 'information,"' . $m . '"';
        $b = IGKCSVDataAdapter::LoadString($v, true, [
            'flags'=>CSVHelper::CSV_READ_SERIAL
        ]);
        $this->assertTrue(count($b[0]) == 2);
        $r = unserialize($b[0][1]);
        $this->assertEquals([1 => "title\ndata"], $r);


        // $v = 'information,"{"data":"hello\\\\nm"}"';
        // $b = IGKCSVDataAdapter::LoadString($v);
        // $this->assertTrue(count($b[0])==2);
        // $c = $b[0][1];
        // $r = json_decode($c); 
        // $this->assertEquals((object)['data'=>"hello\nm"], $r);


    }

    public function test_csv_line_data()
    {
        
  
        $l1 = implode("\n", ['one,"for', "me\""]);
        $l2 = implode("\n", ['tone,"for', "li\""]);
        $src = implode("\n", ['present', $l1, $l2, 'end']);
        $array = igk_csv_readline($src);
        $this->assertEquals([
            'present',
            'one,"for'."\n".'me"',
            'tone,"for'."\n".'li"',
            'end'
        ],$array);

        
        // invalid segment
        $src = implode("\n", ['echo"',"dujour"]);
        $array = igk_csv_readline($src);
        $this->assertTrue(empty($array));


        $src = implode("\n", ['echo"',"dujour\""]);
        $array = igk_csv_readline($src);
        $this->assertEquals(['echo"'."\n".'dujour"'],$array);


        $src = implode("\n", range(1,4));
        $array = igk_csv_readline($src);
        $this->assertEquals(range(1,4),$array);


        $m = serialize([1 => "title\ndata"]);
        $v = 'information,"' . $m . '",oui';

        $array = igk_csv_readline($v, '"', $last, null, CSVHelper::CSV_READ_SERIAL);
        $this->assertEquals(['information,"a:1:{i:1;s:10:"title'."\ndata\";}\",oui"]
            ,$array);
    }
}
