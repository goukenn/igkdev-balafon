<?php
// @author: C.A.D. BONDJE DOUE
// @file: DotEnvConfigurationTest.php
// @date: 20260108 13:39:25
namespace IGK\Tests\System\IO;

use IGK\System\IO\DotEnvConfiguration;
use IGK\Tests\BaseTestCase;

/**
* auto generate doc.
* @package IGK\Tests\System\IO
* @author C.A.D. BONDJE DOUE
*/
class DotEnvConfigurationTest extends BaseTestCase{

    /**
    * Tests dotenv loading ignore comment config.
    */
    public function test_dotenv_loading_ignore_comment_config(){
        //
        $src = implode("\n", [
            '# primary comment'
        ]);
        $configs = [];
        DotEnvConfiguration::LoadConfiguration($configs, $src);
        $this->assertEquals([], $configs, 'ignore comments');
    }

    /**
    * Tests dotenv loading config.
    */
    public function test_dotenv_loading_config(){
        //        
        $src = implode("\n", [
            '# primary comment',
            'primary=true',
            'default_css=primary.css'
        ]);
        $configs = [];
        DotEnvConfiguration::LoadConfiguration($configs, $src);
        $this->assertEquals([
            'primary'=>true,
            'default_css'=>'primary.css'
        ], $configs, 'ignore comments');
    }

    /**
    * Tests dotenv loading string config.
    */
    public function test_dotenv_loading_string_config(){
        //        
        $src = implode("\n", [
            '# primary comment',
            'primary="true"',
        ]);
        $configs = [];
        DotEnvConfiguration::LoadConfiguration($configs, $src);
        $this->assertEquals([
            'primary'=>"true", 
        ], $configs, 'ignore comments');
    }
}