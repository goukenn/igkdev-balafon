<?php
// @author: C.A.D. BONDJE DOUE
// @file: Colorize.php
// @date: 20240914 12:40:24
namespace IGK\System\Console;

use Exception;
use IGK\System\Text\RegexMatcherContainer;
use IGKValidator;

///<summary></summary>
/**
 * 
 * @package IGK\System\Console
 * @author C.A.D. BONDJE DOUE
 */
class Colorize
{
    /**
     * filter listener
     * @var mixed
     */
    var $listener;
    /**
     * store color definition
     * @var mixed
     */
    var $colors;

    protected function _initRegexMatcherContainer(RegexMatcherContainer $match){
        $match->begin("('|\")", "(?<!\\\\)\\1", "string");
        $match->begin("#", "$", "comment");
        $match->match("\\d+(\.\d+)?", "number");
        $match->match("(\\{|\\[)", "marker");
        $match->match("(\\}|\\])", "emarker");
        $match->match("\b(null|true|false)\b", "words");
    }
    protected function _initColor():array{
        return [
            "email"=>"\e[38;2;71;100;244m",
            "string"=>"\e[38;2;151;212;245m",
            "uri"=>"\e[38;2;253;88;55m",
            "comment"=>"\e[38;2;30;154;42m",
            "secret"=>"\e[38;2;30;154;42m",
        ];
    }
    /**
     * 
     * @param mixed $s 
     * @param null|RegexMatcherContainer $match 
     * @param mixed $filter 
     * @return null|string 
     * @throws Exception 
     */
    public function __invoke($s, ?RegexMatcherContainer $match = null, $filter = null):?string
    {
        if (is_null($match)) {
            $match = new RegexMatcherContainer;
            $this->_initRegexMatcherContainer($match);
          
        }
        $rp = [];
        $filter = $filter ?? $this->listener;
        if ($match->extract($s, function ($g) use (&$rp, $filter) {
            if ($filter && $filter($g, $rp)) {
                return true;
            }
            if (!$g){
                return false;
            }
            $c = null; 
            $v_t = [];
            $v_colors = $this->colors ?? $this->_initColor();
            if ($g->tokenID == 'number') {
                $v_t[] = App::Gets(App::YELLOW, $g->value);
            } else {
                switch ($g->tokenID) {
                    case 'emarker':
                    case 'marker':
                        $v_t[] = App::Gets("\e[38;2;153;35;89m", trim($g->value));
                        break;
                    case 'words':
                        $v_t[] = App::Gets("\e[38;2;50;80;229m", trim($g->value));
                        break;
                    case 'comment':
                        $v_t[] = App::Gets(igk_getv($v_colors,$g->tokenID, App::GREEN), trim($g->value));
                    case 'string':
                        //treat string
                        $v = $g->value;
                        $v = preg_replace("/(((http(s)?|ftp|sw|ssl|file):)?\/\/[^\s\"]+)/", App::Gets(igk_getv($v_colors,'uri'), "\$1"), $v);
                        $v = preg_replace("/([a-z0-9\.\-_]+@[a-z0-9\.\-_]+\.[a-z]{2,6})/", App::Gets(igk_getv($v_colors,'email'), "\$1"), $v);
                        $v = preg_replace("/(\< secret \>)/", App::Gets(igk_getv($v_colors,'secret'), "\$1"), $v);
                        $ac = igk_getv($v_colors,$g->tokenID, App::RED);
                        $v = str_replace("\e[0m", $ac, $v);
                        $v_t[] = App::Gets($ac, $v);
                        break;
                    default:
                        $v_t[] = App::Gets(igk_getv($v_colors,$g->tokenID, App::RED), $g->value);
                        break;
                }
            }
            if ($v_t){
                array_unshift($v_t, $g->from, $g->to);
                $rp[] = $v_t;
            }
            return true;
        })) {

            $n = '';
            $offset = 0;
            while (count($rp) > 0) {
                list($from, $to, $value) = array_shift($rp);
                $n .=  substr($s, $offset, $from - $offset) . $value;
                $offset = $to;
            }
            $n .= substr($s, $offset);
            $s = $n;
        }
        return $s;
    }
}
