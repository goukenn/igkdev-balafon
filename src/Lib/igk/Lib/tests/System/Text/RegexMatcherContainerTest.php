<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherContainerTest.php
// @date: 20240913 10:19:21
namespace IGK\Tests\System\Text;

use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
 * 
 * @package IGK\Tests\System\Text
 * @author C.A.D. BONDJE DOUE
 */
class RegexMatcherContainerTest extends BaseTestCase
{
    public function test_regexmatch_list()
    {
        $container = new RegexMatcherContainer;
        $container->match("\\b(hello|friend)\\b");
        $pos = 0;
        $src = "hello my friend!";
        $match = [];
        while ($g = $container->detect($src, $pos)) {
            $g = $container->end($g, $src, $pos);
            $match[] = $g->value;
        }

        $this->assertEquals('hello friend', implode(' ', $match));
    }

    public function test_regexmatch_detect_htmlclass()
    {
        $container = new RegexMatcherContainer;
        $container->begin("<!--", "-->", "comment");
        $container->begin("<(?:\\w+)", ">", "tag");
        $pos = 0;
        $src = "<!-- start definition -->hello my friend!<div className=\"card      presentation\"></div>";
        $match = [];
        while ($g = $container->detect($src, $pos)) {
            $g = $container->end($g, $src, $pos);

            switch ($g->tokenID) {
                case 'tag':
                    $def = (object)['ref' => null];
                    $con = new RegexMatcherContainer;
                    $con->begin("\bclass(Name)?\b\s*", "=\s*", 'class');
                    $con->begin('("|\')', "\\1", "value");

                    $part = $con->extract($g->value, function ($g) use ($def) {
                        if ($def->ref) {
                            if ($g->tokenID == 'value') {
                                $def->ref = null;
                                $g->value = preg_replace("/\s+/", " ", $g->value);
                                return true;
                            }
                        } else {
                            if ($g->tokenID == 'class') {
                                $def->ref = true;
                            }
                        }
                        return false;
                    });
                    if ($part) {
                        $match = array_merge($match, $part);
                    }
                    break;
            }
        }

        $this->assertEquals('"card presentation"', implode(' ', $match));
    }

    private function _regexDetectFuncData()
    {
        $ctn = new RegexMatcherContainer;
        $l = $ctn->begin('\w+\b\(', ';|(?<=})', 'func-block')->last();
        $subblock = $ctn->begin('{', '}', 'subblock')->last();
        $string =   $ctn->appendStringDetection()->last();
        $l->patterns = [
            $subblock,
            $string
        ];
        $subblock->patterns = [
            $string,
            $subblock
        ];
        return $ctn;
    }
    public function test_regexmatch_detect_func()
    {
        $s = implode("\n", [
            "a(){ info = {} } }"
        ]);
        $ctn = $this->_regexDetectFuncData();
        $this->expectOutputString('a(){ info = {} }');
        $ctn->treat($s, function ($g) {
            if ($g->parentInfo == null)
                echo $g->value;
        });
    }
    public function test_regexmatch_detect_func_2()
    {
        $s = implode("\n", [
            "a(){ info = '{}' } }"
        ]);
        $ctn = $this->_regexDetectFuncData();
        $this->expectOutputString('a(){ info = \'{}\' }');
        $ctn->treat($s, function ($g) {
            if ($g->parentInfo == null)
                echo $g->value;
        });
    }



    private function _regexDetectDeclareFuncData()
    {
        $ctn = new RegexMatcherContainer;
        $l = $ctn->begin('\w+\b\(', ';', 'func-block')->last();
        $subblock = $ctn->begin('{', '}', 'subblock')->last();
        $string =   $ctn->appendStringDetection()->last();
        $l->patterns = [
            $subblock,
            $string
        ];
        $subblock->patterns = [
            $string,
            $subblock
        ];
        return $ctn;
    }

    public function test_regexmatch_detect_declare_func()
    {
        $s = implode("\n", [
            "a({x:string}): string; "
        ]);
        $ctn = $this->_regexDetectDeclareFuncData();
        $this->expectOutputString('a({x:string}): string;');
        $ctn->treat($s, function ($g) {
            if ($g->parentInfo == null)
                echo $g->value;
        });
    }
    /**
     * 
     * @return void 
     */
    public function test_regexmatch_skip_multiline()
    {
        // phpunit -c phpunit.xml.dist --testsuite core --filter test_regexmatch_multiline
        $s = implode("\n", [
            "a",
            "b",
            "c"
        ]);
        $ctn = new RegexMatcherContainer;
        $ctn->match("\\b(a|c)\\b");
        $this->expectOutputString(implode("\n", ['a', 'c', 'b']));
        $pos = 0;
        $ctn->treat($s, function ($g, $next_pos, $data,) use (&$ch, &$pos) {
            if ($g->parentInfo == null)
                echo $g->value . "\n";
            RegexMatcherUtility::Skip($g, $next_pos, $data, $pos, $ch);
        });
        echo trim($ch);
    }
    public function test_regexmatch_skip_multiline_litteral()
    {
        // phpunit -c phpunit.xml.dist --testsuite core --filter test_regexmatch_multiline
        $s = implode("\n", [ 
            "(a:string):void;",
            "b: string;",
            "new (b:string):void;",
            "cm: string;"
        ]);
        $ctn = new RegexMatcherContainer;
        $brank_function = $ctn->begin("(new\\b)?\\s*(?=\()", ";", "brank-func")->last(); 

        $brank = $ctn->appendBrank()->last(); 
        $brank_function->patterns = [
            $brank
        ];
        $this->expectOutputString(implode("\n", [
            '(a:string):void;',
            'new (b:string):void;',
            'b: string;',
            'cm: string;'
        ]));
        $pos = 0;
        $ctn->treat($s, function ($g, $next_pos, $data,) use (&$ch, &$pos) {
            if ($g->parentInfo == null) {
                echo $g->value . "\n";
                RegexMatcherUtility::Skip($g, $next_pos, $data, $pos, $ch);
            }
        });
        $ch .= substr($s, $pos);
        echo trim($ch);
    }

    public function test_regexmatch_skip_glue()
    {
        // phpunit -c phpunit.xml.dist --testsuite core --filter test_regexmatch_skip_glue
        $s = implode("\n", [ 
            // " 'a' | 'b'; ",
            " 'a' | 'b' ",
            "|",
            "{ b:string }",
            "export {type a}"
        ]);
        $ctn = new RegexMatcherContainer;
        $brank_function = $ctn->begin("\{", "\}", "brank-func")->last(); 
        $glue = $ctn->match("(?<=(\}|'|\"))?\|", 'glue')->last(); 
        $str = $ctn->appendStringDetection()->last(); 
        $stop = $ctn->match("(?=;|^\w+)", 'stop-def')->last(); 
        // $ctn->match('(?=\\w+|[^\\w\\s])', 'end');



        $this->expectOutputString(implode("\n", [
            'string:\'a\'',
            'glue:|',
            'string:\'b\'',
            'glue:|',
            'brank-func:{ b:string }',
            'stop-def:',
            ''
        ]));
        $pos = 0;
        $ch = '';
        $ctn->treat($s, function ($g, $next_pos, $data) use (&$ch, &$pos) {
            if ($g->parentInfo == null) {
                echo $g->tokenID.':'.$g->value . "\n";
                RegexMatcherUtility::Skip($g, $next_pos, $data, $pos, $ch);
                if ($g->tokenID == 'stop-def'){
                    return true;
                }
            }
        });
        //$ch .= substr($s, $pos);
     
    }

    public function test_regexmatch_startline(){
        $ctn = new RegexMatcherContainer;
        $ctn->match("^b");
        $s = implode("\n", ["a","b", "c"]);
        $this->expectOutputString("b", "mark-name");

        $ctn->treat($s, function($e){
            echo $e->value;
        });
    }
    public function test_regexmatch_startline_2(){
        $ctn = new RegexMatcherContainer;
        $ctn->match("^(b|a)");
        $s = implode("\n", ["a","b", "c"]);
        $this->expectOutputString("ab", "mark-name");

        $ctn->treat($s, function($e){
            echo $e->value;
        });
    }
}
