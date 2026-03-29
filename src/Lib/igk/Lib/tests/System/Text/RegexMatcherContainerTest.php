<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherContainerTest.php
// @date: 20240913 10:19:21
namespace IGK\Tests\System\Text;

use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;
use IGK\Tests\BaseTestCase;

/**
 * 
 * @package IGK\Tests\System\Text
 * @author C.A.D. BONDJE DOUE
 */

/**
* auto generate doc.
* @package IGK\Tests\System\Text
*/
class RegexMatcherContainerTest extends BaseTestCase
{

    /**
    * Tests regexmatch list.
    */
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

    /**
    * Tests regexmatch detect htmlclass.
    */
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

    /**
    * auto generate doc.
    * @return
    */
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

    /**
    * Tests regexmatch detect func skip trailing close.
    */
    public function test_regexmatch_detect_func_skip_trailing_close()
    {
        $s = implode("\n", [
            "a(){ info = {} } }"
        ]);
        $ctn = $this->_regexDetectFuncData();
        $this->expectOutputString('a(){ info = {} }');
        $ctn->treat($s, function ($g) {
            if ($g->getisRootCaptured())
                echo $g->value;
        });
    }

    /**
    * Tests regexmatch detect func 2.
    */
    public function test_regexmatch_detect_func_2()
    {
        $s = implode("\n", [
            "a(){ info = '{}' } }"
        ]);
        $ctn = $this->_regexDetectFuncData();
        $this->expectOutputString('a(){ info = \'{}\' }');
        $ctn->treat($s, function ($g) {
            if ($g->getisRootCaptured())
                echo $g->value;
        });
    }

    /**
    * auto generate doc.
    * @return
    */
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

    /**
    * Tests regexmatch detect declare func.
    */
    public function test_regexmatch_detect_declare_func()
    {
        $s = implode("\n", [
            "a({x:string}): string; "
        ]);
        $ctn = $this->_regexDetectDeclareFuncData();
        $this->expectOutputString('a({x:string}): string;');
        $ctn->treat($s, function ($g) {
            if ($g->getisRootCaptured())
                echo $g->value;
        });
    }

    /**
    * auto generate doc.
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

    /**
    * Tests regexmatch skip multiline litteral.
    */
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
            if (($g->parentInfo == null) && ($g->tokenID != '__end:test__')) {
                echo $g->value . "\n";
                RegexMatcherUtility::Skip($g, $next_pos, $data, $pos, $ch);
            }
        }, '__end:test__');
        $ch .= substr($s, $pos);
        echo trim($ch);
    }

    /**
    * Tests regexmatch skip glue.
    */
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
                echo $g->tokenID . ':' . $g->value . "\n";
                RegexMatcherUtility::Skip($g, $next_pos, $data, $pos, $ch);
                if ($g->tokenID == 'stop-def') {
                    return true;
                }
            }
        });
        //$ch .= substr($s, $pos);

    }

    /**
    * Tests regexmatch startline b only.
    */
    public function test_regexmatch_startline_b_only()
    {
        $ctn = new RegexMatcherContainer;
        $ctn->match("^b");
        $s = implode("\n", ["a", "b", "c"]);
        $this->expectOutputString("b", "mark-name");

        $ctn->treat($s, function ($e) {
            if ($e->getisRootCaptured())
                echo $e->value;
        });
    }

    /**
    * Tests regexmatch startline ba only.
    */
    public function test_regexmatch_startline_ba_only()
    {
        $ctn = new RegexMatcherContainer;
        $ctn->match("^(b|a)");
        $s = implode("\n", ["a", "b", "c"]);
        $this->expectOutputString("ab", "mark-name");

        $ctn->treat($s, function ($e) {
            if ($e->getisRootCaptured())
                echo $e->value;
        });
    }

    /**
    * Tests regexmatch number line.
    */
    public function test_regexmatch_number_line()
    {
        $container = new RegexMatcherContainer;
        $container->match('^\d+(?=\n)?', 'count');
        $src = implode("\n", range(1, 6));
       
        $r = []; 
        $container->treat($src,  function ($g, $next_pos) use (&$r) {
            $r[] = "capture : " . $next_pos . ":" . $g->tokenID . ": " . $g->value;
        });
        $this->assertEquals('["capture : 1:count: 1","capture : 3:count: 2","capture : 5:count: 3","capture : 7:count: 4","capture : 9:count: 5","capture : 11:count: 6"]', json_encode($r));
    }

    /**
    * Test regexmatch empty line.
    * no space inside.
    */
    public function test_regexmatch_empty_line()
    {
        $container = new RegexMatcherContainer;
        $container->match('^(?=\\n)?', 'count');
        $src = str_repeat("\n", 6);         
        $r = []; 
        igk_debug(true);
        $container->treat($src, function ($g, $next_pos) use (&$r) {
            $r[] = ("> : " . $next_pos . ":" . $g->tokenID . ": " . $g->value);
            });
        igk_debug(false);
          
        $this->assertEquals('["> : 1:count: ","> : 2:count: ","> : 3:count: ","> : 4:count: ","> : 5:count: ","> : 6:count: "]', 
            json_encode($r)
        );
    }

    /**
    * auto generate doc.
    * @return void
    */

    public function test_regexmatch_empty_block()
    { 
        $regex = new RegexMatcherContainer;

        // ''  stop a end of the source text  
        // '$' stop at end of the line 
        $c = $regex->createPattern([
            // "begin"=>"begin",
            // "end"=>"", //  "" or "$" "to stop" 
            "tokenID" => 'marking',
            "patterns" => [
                $regex->createPattern(['match' => "\\ba\\b", "tokenID" => 'letter-a']),
                $regex->createPattern(['match' => "\\bb\\b", "tokenID" => 'letter-b']),
            ]
        ]);

        $h = $regex->createPattern([
            "begin" => "begin",
            "end" => "$", //  "" or "$" "to stop" 
            "tokenID" => "group-block",
            "patterns" => [$c]
        ]);

        $regex->append($h);
        $src = implode("\n", [
            "begin a de jour b a b",
            "home",
            "begin racagnac a a",
        ]);
        $pos = 0;
        $this->expectOutputString(implode("\n", [
            'letter-a',
            'letter-b',
            'letter-a',
            'letter-b',
            'group-block',
            'letter-a',
            'letter-a',
            'group-block',
            ''
        ]));
        while ($g = $regex->detect($src, $pos)) {

            if ($e = $regex->end($g, $src, $pos)) {
                echo ($e->tokenID) . PHP_EOL;
            }
        }
    }
    private function _stop_detector(){
        $regex = new RegexMatcherContainer;
        $regex->begin('begin:', '$', 'mark')->last()->patterns = [
            ['match' => '(?=!)', 'tokenID'=>'stop-end']
        ];
        return $regex;
    }

    /**
    * Tests regexmatch detect stop.
    */
    public function test_regexmatch_detect_stop()
    {
        $src = "begin: one ! begin: test is! ok\nlogo begin: gesture is for beginner\nbegin: data ok";

        $regex =  $this->_stop_detector();

        $pos = 0;
        $rp = [];
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                if ($e->tokenID == 'mark') {
                    $rp[] = $e->value;
                }
            }
        }
        $this->assertEquals([
'begin: one ', 'begin: gesture is for beginner', 'begin: data ok'
        ], $rp);
        // |begin: one |begin: gesture is for beginner|begin: data ok
    }

    /**
    * Tests regexmatch detect append after end stop.
    */
    public function test_regexmatch_detect_append_after_end_stop()
    {
        $src = "begin: one ; cause ! begin: data ok";
       //  $src = "begin: data ok";

        $regex = new RegexMatcherContainer;
        $regex->begin('begin:', ';', 'mark')->last()->patterns = [
            ['match' => '(?=!)', 'tokenID'=>'stop-end']
        ];

        $pos = 0;
        $rp = [];
        igk_debug(true);
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                if ($e->tokenID == 'mark') {
                    $rp[] = $e->value;
                }
            }
        }
        igk_is_debug(false);
        $this->assertEquals([
            'begin: one ;', 'begin: data ok'
        ], $rp);
    }

    /**
    * Tests regexmatch detect append after end stop 2.
    */
    public function test_regexmatch_detect_append_after_end_stop_2()
    {
        $src = "       g///<summary>info</summary>\nbegin: ";
        //.$stop = ['match'=>'^\\s*[^\/\\s]+', 'tokenID'=>'ugly-line'];
        $regex = new RegexMatcherContainer;
        $inner = $regex->begin('>','(?=<)', 'inner-sub')->last();
        $inner->patterns = [ 
            //$stop
        ];
        // + | for every line that start with /// or empty arch
        $regex->begin('(?:^\\s*|(?<=[^\/]))\/\/\/<(summary)', '<\/\\1>', 'mark')->last()->patterns = [
           $inner
        ];
        $pos = 0;
        $rp = []; 
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                if ($e->tokenID == 'mark') {
                    $rp[] = $e->value; 
                } 
            }
        }
        $this->assertEquals([
'///<summary>info</summary>'
        ], $rp);
    }

}
