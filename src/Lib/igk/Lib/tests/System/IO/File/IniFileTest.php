<?php 

namespace IGK\Tests\System\IO\File;

use IGK\System\IO\File\IniFile;
use IGK\Tests\BaseTestCase;

class IniFileTest extends BaseTestCase{
    public function test_create_file(){
        $temp = sys_get_temp_dir()."/tempfile.ini";
        igk_io_w2file($temp, "info=12");
        $g = IniFile::LoadConfig($temp);

        
        $this->assertEquals(
            ["info"=>"12"]
            , $g->to_array()
        ) ;
        unlink($temp); 
    }


    public function test_comment_out_file(){
        $temp = sys_get_temp_dir()."/tempfile.ini";
        igk_io_w2file($temp, "info=12");
        $g = IniFile::LoadConfig($temp);
        $g->comment("info");
        $this->assertEquals(
            ["#info"=>"12"]
            , $g->to_array()
        ) ;

        $g->activate("info");
        $this->assertEquals(
            ["info"=>"12"]
            , $g->to_array(),
            "activation failed ..... "
        );

        unlink($temp); 
    }
 
}