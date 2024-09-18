<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherContainerTest.php
// @date: 20240913 10:19:21
namespace IGK\Tests\System\Text;

use IGK\System\Text\RegexMatcherContainer;
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

            switch($g->tokenID ){
                case 'tag':
                    $def = (object)['ref'=>null];
                    $con = new RegexMatcherContainer;
                    $con->begin("\bclass(Name)?\b\s*", "=\s*",'class'); 
                    $con->begin('("|\')', "\\1", "value"); 

                    $part = $con->extract($g->value, function($g)use($def){
                        if ($def->ref){
                            if ($g->tokenID =='value'){
                                $def->ref = null;
                                $g->value = preg_replace("/\s+/", " ", $g->value);
                                return true;
                            }
                        } else{
                            if ($g->tokenID=='class'){
                                $def->ref = true;
                            }
                        }
                        return false;
                    });
                    if ($part){
                        $match = array_merge($match, $part);
                    } 
                    break;
            } 
        }

        $this->assertEquals('"card presentation"', implode(' ', $match));
    }
}
