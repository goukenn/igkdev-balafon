<?php
// @author: C.A.D. BONDJE DOUE
// @file: NodeBuilderGenerateTest.php
// @date: 20230402 12:20:54
namespace IGK\Tests\System\Html;

use IGK\System\Html\HtmlNodeBuilder;
use IGK\Tests\BaseTestCase;
use IGKException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Html
*/
class NodeBuilderGenerateTest extends BaseTestCase{

    public function test_generate(){
        $d = igk_create_node();
        $this->assertEquals(sprintf('$builder(%s);', igk_array_dump_short([
            "div"=>[]
        ])), 
        HtmlNodeBuilder::Generate($d));
    }
    /**
     * different create_node and ref node resolution 
     * @return void 
     * @throws IGKException 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function test_generate_1(){
        $d = igk_create_node('div.class');
        $this->assertEquals(sprintf('$builder(%s);', igk_array_dump_short([
            "div.class"=>[]
        ]), 'igk_create_node must igk_create_rnode'), 
        HtmlNodeBuilder::Generate($d));
    }
    public function test_generate_class(){
        $d = igk_create_rnode('div.main.sample#info%list');
        $this->assertEquals(
            '$builder(["div#info%list.main.sample"=>[]]);',
        HtmlNodeBuilder::Generate($d));
    }
    public function test_generate_class_hello(){
        $d = igk_create_rnode('div.main.sample#info%list');
        $d->div()->Content = "Hello";
        $this->assertEquals(
            '$builder(["div#info%list.main.sample"=>["div"=>"Hello"]]);',
        HtmlNodeBuilder::Generate($d));
    }
    public function test_generate_class_gen(){
        $d = igk_create_rnode('div.main.sample#info%list');
        $d->div()->Content = "Hello";
        $d->div()->Content = "Friend";
        $this->assertEquals(
            '$builder(["div#info%list.main.sample"=>["div"=>"Hello",["@_t:div"=>"Friend"]]]);',
        HtmlNodeBuilder::Generate($d));
    }
    public function test_generate_class_stagen(){
        $d = igk_create_rnode('div.main.sample#info%list');
        $d->add("div > div > span > quote")->Content = "Hello";
     
        $this->assertEquals(
            '$builder(["div#info%list.main.sample"=>["div"=>["div"=>["span"=>["quote"=>"Hello"]]]]]);',
        HtmlNodeBuilder::Generate($d));
    }
}
