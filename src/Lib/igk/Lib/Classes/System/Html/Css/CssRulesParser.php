<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssRulesParser.php
// @date: 20250628 22:08:34
namespace IGK\System\Html\Css;
use Exception;
use IGK\System\Console\Logger;
use IGK\System\Text\RegexMatcherContainer;
/**
 * parse css string content and return and array of string 
 * @package IGK\System\Html\Css
 * @author C.A.D. BONDJE DOUE
 */
class CssRulesParser
{

    /**
    * Property: regex.
    * @var mixed
    */
    private $m_regex;

    /**
    * .ctr
    * @return
    */
    private function __construct()
    {
        $this->m_regex = $this->_initRegexContainer();
    }

    /**
    * Init regex container.
    */
    protected function _initRegexContainer()
    {
        $c = new RegexMatcherContainer;
        $c->autoStore = false;
        $glue_space = $c->match('\\s+', 'glue-space')->last();
        $brank = $c->begin('\{', '\}', 'brank')->last();
        $media_brank = $c->begin('\{', '\}', 'media-brank')->last();
        $attrib = $c->begin('\[', '\]', 'attrib')->last();
        $func = $c->begin('\(', '\)', 'func')->last();
        $glue = $c->match('\\s*(\+|>|~)\\s*', 'glue')->last();
        $separator = $c->match('\\s*(,)\\s*', 'separator')->last();
        $string = $c->appendStringDetection()->last();
        $comment = $c->begin('\/\*', '\*\/', 'comment')->last();
         $at_rule = $c->begin('@\\w+\\b', '(?<=\}|;)', 'at-rule')->last();
        $media = $c->begin('@media\\b', '(?<=\})', 'media')->last();
        $media->patterns = [
            $c->createPattern(['match'=>"\\s+(print|and|screen)"]),
            $comment,
            $glue_space,
            $string,
            $media_brank
        ];
         $at_rule->patterns = [
 $comment,
            $glue_space,
            $string,
            $media_brank
         ];
        $selector = $c->match('\*|(\.|#|:+)?[\\w\-]+(-|\\b)', 'selector')->last();
        $c->autoStore = true;
        $media_brank->patterns = [
            $string,
            $glue_space,
            $comment,
            $media_brank,
        ];
        $brank->patterns = [
            $string,
            $glue_space,
            $comment,
            $media_brank,
        ];
        $c->append($comment);
        $c->append($attrib);
        $c->append($media);
        $c->append($selector);
        $c->append($string);
        $c->append($glue);
        $c->append($func);
        $c->append($brank);
        $c->append($at_rule);
        $c->append($separator);
        $c->append($glue_space);
        return $c;
    }

    /**
    * auto generate doc.
    * @param string $src
    * @return array
    */

    public static function Parse(string $src): array
    {
        $tab = [];
        $c = new static;
        $regex = $c->m_regex;
        $pos = 0;
        $c = '';
        $buffers = [];
        $data_fc = function (& $buffers, $from, $v, $src) {
            if (empty($buffers)){
                return $v;
            }
            $offset = $from;
            $ns = '';
            while (count($buffers) > 0) {
                $c = array_shift($buffers);
                $ns .= substr($src, $offset, $c[0] - $offset) . $c[2];
                $offset = $c[1];
            }
            $ns .= substr($v, $offset-$from);
            return $ns;
        };
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                // Logger::info($e->tokenID);
                $v = $e->value;
                switch ($e->tokenID) {
                    case 'at-rule':
                        $buffers = [];
                        break;
                    case 'comment':
                        break;
                    case 'brank':
                        $tab[] = trim($c) . trim($data_fc($buffers,$e->from, $v, $src));
                        $c = '';
                        break;
                    case 'media': 
                        $v_th = $data_fc($buffers,$e->from,$v ,$src);
                        array_unshift($tab, $v_th);
                        $c = '';
                        break;
                    case 'media-brank':
                        break;
                    default:
                        if ($e->tokenID == 'glue') {
                            $v = trim($v);
                        }
                        if ($e->tokenID == 'glue-space') {
                            $v = $e->parentInfo == null ? ' ':'';
                        }
                        if ($e->parentInfo == null) {
                            $c .= $v;
                        } else {
                            $buffers[] = [$e->from, $e->to, $v];
                        }
                        break;
                }
            }
        }
        if (!empty($c)) {
        }
        return $tab;
    }
}