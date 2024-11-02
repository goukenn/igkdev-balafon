<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlColorizer.php
// @date: 20241030 18:39:33
namespace IGK\System\Console\Html;

use IGK\System\Console\Colorize;
use IGK\System\Text\RegexMatcherContainer;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Html
* @author C.A.D. BONDJE DOUE
*/
 /**
 * create html colorizer
 * @package 
 */
class HtmlColorizer extends Colorize{

    protected function _initColor(): array
    {
        return array_merge(parent::_initColor(), [
           'tagname'=>"\e[38;2;170;65;30m" 
        ]);

    }
    protected function _initRegexMatcherContainer(RegexMatcherContainer $match)
    {
        parent::_initRegexMatcherContainer($match);
        $match->begin('<script\\b', '<\/script>', 'tagname script-content');
        $match->match('<[\w-]+', 'tagname');
        $match->match('<\/[\w-]+\\s*>', 'tagname');
        $match->match('\/>', 'end-tagname');
        $match->match('<[\w-]+\\s*\/>', 'empty-tagname');
        $match->begin('<!--', '-->', 'comment');
        $match->match('(?i)<!DOCTYPE\b', 'tagname');
        $match->match('>', 'tagname');
      
    }
    
    /**
     * 
     * @param mixed $s 
     * @param null|RegexMatcherContainer $match 
     * @param mixed $filter 
     * @return ?string 
     * @throws Exception 
     */
    public function __invoke($s, ?RegexMatcherContainer $match = null, $filter = null): ?string
    {
        return parent::__invoke($s,$match, $filter);
    }
}

