<?php


// @author: C.A.D. BONDJE DOUE
// @filename: ConfigurationReaderTest.php
// @date: 20220830 09:50:18
// @desc: 

namespace IGK\Test\System\IO\Configuration;

use IGK\System\IO\Configuration\ConfigurationEncoder;
use IGK\System\IO\Configuration\ConfigurationReader;
use IGK\Tests\BaseTestCase;

/**
 * test configuration reader function 
 * @package IGK\Test\System\IO\Configuration
 */
class ConfigurationReaderTest extends BaseTestCase {
    public function test_read_connexion_string(){
        $connexion = "user=root, pwd=test, db_name=sample.db, charset=utf-8";
        $obj = (object)[
            "user"=>"root",
            "pwd"=>"test",
            "db_name"=>"sample.db",
            "charset"=>"utf-8"
        ];
        $reader = new ConfigurationReader;
        $config = (object)$reader->read($connexion);
        $this->assertEquals(
            json_encode($obj),
            json_encode($config),
            "can't read connexion string"
        );
    }

    public function test_read_connexion_string_with_litteral(){
        $connexion = "user=root, pwd='test,presentation', db_name=sample.db, charset=utf-8";
        $obj = (object)[
            "user"=>"root",
            "pwd"=>"test,presentation",
            "db_name"=>"sample.db",
            "charset"=>"utf-8"
        ];
        $reader = new ConfigurationReader;
        $config = (object)$reader->read($connexion);
        $this->assertEquals(
            json_encode($obj),
            json_encode($config),
            "can't read connexion string"
        );
    }

    public function test_read_css_style(){
        $connexion = "background-color:red; color:white;";
        $obj = (object)[
            "background-color"=>"red",
            "color"=>"white", 
        ];
        $reader = new ConfigurationReader;
        $reader->separator = ':';
        $reader->delimiter = ';';
        $config = (object)$reader->read($connexion);
        $this->assertEquals(
            json_encode($obj),
            json_encode($config),
            "can't read css style string"
        );
    }
    public function test_read_environment_value(){
        $connexion = "BASE_URL=https://localhost.com\nPRESENTATION=\nINFO=BALAFON";
        $obj = (object)[
            "BASE_URL"=>"https://localhost.com",
            "PRESENTATION"=>null, 
            "INFO"=>"BALAFON", 
        ];
        $reader = new ConfigurationReader;
        $reader->separator = '=';
        $reader->delimiter = "\n";
        $config = (object)$reader->read($connexion);
        $this->assertEquals(
            json_encode($obj),
            json_encode($config),
            "can't read css style string"
        );
    }
    public function test_read_environment_value_2(){
        //add empty space so value ca be empty 
        $connexion = "BASE_URL=https://localhost.com\nPRESENTATION= \nINFO=BALAFON";
        $obj = (object)[
            "BASE_URL"=>"https://localhost.com",
            "PRESENTATION"=>"", 
            "INFO"=>"BALAFON", 
        ];
        $reader = new ConfigurationReader;
        $reader->separator = '=';
        $reader->delimiter = "\n";
        $config = (object)$reader->read($connexion);
        $this->assertEquals(
            json_encode($obj),
            json_encode($config),
            "can't read css style string"
        );
    }
    public function test_remove_str_mark(){
        $this->assertEquals(
            'bonjour',
            ConfigurationReader::RmStringMark("'bonjour'"),
            "the str mark test faile 1"
        );
        $this->assertEquals(
            '\'bonjour',
            ConfigurationReader::RmStringMark("'bonjour"),
            "the str mark test faile 2"
        );
        $this->assertEquals(
            'bonjour',
            ConfigurationReader::RmStringMark('"bonjour"'),
            "the str mark test faile 3"
        );
        $this->assertEquals(
            '\"bonjour\"',
            ConfigurationReader::RmStringMark('\"bonjour\"'),
            "the str mark test faile 4"
        );
    }   
}
