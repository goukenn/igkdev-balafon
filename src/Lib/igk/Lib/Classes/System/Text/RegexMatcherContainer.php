<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherContainer.php
// @date: 20240913 10:19:11
namespace IGK\System\Text;

use Exception;
use IGK\Helper\Activator;
use IGK\System\Text\RegexMatcherPattern;
use IGKException;

///<summary></summary>
/**
 * extract definitio beetween begin/end definition 
 * @package IGK\System\Text
 * @author C.A.D. BONDJE DOUE
 */
class RegexMatcherContainer
{
    const REGEX_OPTION = '/^\(\?\b(?P<add>i(m|x|(mx|xm)?)|m(i|x|(ix|xi))?|x(i|m|(im|mi))?)\b(:\b(?P<remove>i(m|x|(mx|xm)?)|m(i|x|(ix|xi))?|x(i|m|(im|mi))?)\b)?\)/';
    const REGEX_START_LINE = '/(?<!\\\\|\w|\[)\^/';
    /**
     * 
     * @var mixed
     */
    private $m_parent;

    /**
     * last empty capture capture
     * @var mixed
     */
    private $m_last_info;
    /**
     * get last inserted match information 
     * @return ?RegexMatcherPattern
     * @throws Exception 
     */
    public function last()
    {
        $c = count($this->m_matcher);
        return $c > 0 ? igk_getv($this->m_matcher, $c - 1) : null;
    }
    /**
     * 
     * @var array
     */
    private $m_matcher = [];
    /**
     * store matching references
     * @var array
     */
    private $m_references = [];
    /**
     * last position 
     * @var ?int
     */
    private $m_pos;

    /**
     * 
     * @param string $id 
     * @return mixed 
     * @throws Exception 
     */
    public function getMatcherByRefId(string $id)
    {
        return igk_getv($this->m_references, $id);
    }
    /**
     * clear all 
     * @return void 
     */
    public function clear()
    {
        $this->m_matcher = [];
        $this->m_references = [];
    }

    /**
     * do end operation 
     * @param mixed $info object info class 
     * @param mixed $source 
     * @param int &$offset 
     * @return object|void 
     * @throws Exception 
     */
    public function end($info, $source, int &$offset = 0)
    {
        $tabinfo = [$info];
        // skip offset update 
        $skip = $this->m_parent === $info;
        $v_size = strlen($info->value);
        if ($v_size == 0) {
            /// TODO: TREAT matching 
            // detect last end pattern 
            if ($info->match === $this->m_last_info) {
                if ($this->m_parent == null) {
                    $i = strpos($source, "\n", $offset);
                    if ($i == -1) {
                        $offset = strlen($source) + 1;
                    } else
                        $offset++;
                    return null;
                } 
                if (!$info->match->patterns)
                    throw new IGKException('--end found: skip: infinite loop---');
            }
            $this->m_last_info = $info->match;
        } else {
            $this->m_last_info = null;
        }

        while (count($tabinfo) > 0) {
            $info = array_shift($tabinfo);
            $k = $info->match;
            $v_size = strlen($info->value);
            // + | update parent info - 
            $this->m_parent = $info->parent;
            switch ($k['type']) {
                case 'begin/end':
                    $b = igk_getv($k, 'end');
                    $o = '';
                    if ($b) {
                        // + | is begin 
                        $b = $b ? sprintf("/%s/%s", $b, $o) : null;
                    }
                    // determine compared position 

                    // + | end back reference
                    $b = preg_replace_callback("/\\\\(?P<id>\d+)/", function ($m) use ($info) {
                        $id = intval($m['id']);
                        return $info->captures[$id][0];
                    }, $b);
                    $v_skipped =  $skip;
                    if (!$skip)
                        $offset += $v_size; // update offset to check end 
                    else
                        $skip = false;
                    $cpos = $offset;
                    $compared_end = ($cpos >= $offset) && $info->match->patterns ? $this->_comparedPattern($info->match->patterns, $source, $cpos) : null;

                    if (preg_match($b, $source, $tab, PREG_OFFSET_CAPTURE, $offset)) {
                        $v_current_offset = $tab[0][1];
                        $n = $v_current_offset  + strlen($tab[0][0]);
                        // + | if empty and offset not change then update to next 
                        if (empty($tab[0][0]) && !$v_skipped && ($v_current_offset == $offset)) {
                            $offset++; //move forward to detect the real next end that match condition
                            array_unshift($tabinfo, $info);
                            $skip = true;
                            continue 2;
                        }
                        // check of compared_end first match 
                        // $tln = $info->pos + strlen($info->value); 
                        if ($compared_end && ($compared_end->pos < $n)) {
                            $offset = $compared_end->pos; // go back to current pos then check end 
                            $compared_end->parent = $info;
                            array_unshift($tabinfo, $compared_end);
                            continue 2;
                            //return $this->end($compared_end, $source);
                        }
                        // update next offset 
                        $offset = $n;
                        return Activator::CreateNewInstance(RegexMatcherCapture::class, [
                            'tokenID' => $k['tokenID'],
                            'from' => $info->pos,
                            'to' => $n,
                            'value' => substr($source, $info->pos, $n - $info->pos),
                            'beginCaptures' => $info->captures,
                            'endCaptures' => $tab,
                            'parentInfo' => $info->parent
                        ]);
                    } else {
                        // move to next line 
                        if (false !== ($l = strpos($source, "\n", $offset))) {
                            $offset = $l + 1; //move forward to detect the real next end that match condition
                            array_unshift($tabinfo, $info);
                            $skip = true;
                            continue 2;
                        }

                        $offset = strlen($source);
                        return Activator::CreateNewInstance(RegexMatcherCapture::class, [
                            'tokenID' => $k['tokenID'],
                            'from' => $info->pos,
                            'to' => $offset,
                            'value' => substr($source, $info->pos),
                            'beginCaptures' => $info->captures,
                            'parentInfo' => $info->parent
                        ]);
                    }
                    break;
                case 'match':
                    $n = $info->pos + $v_size;
                    $offset = $n;
                    return Activator::CreateNewInstance(RegexMatcherCapture::class, [
                        'tokenID' => $k['tokenID'],
                        'from' => $info->pos,
                        'to' => $n,
                        'value' => substr($source, $info->pos, $n - $info->pos)
                    ]);
                    break;
            }
        }
    }
    private function _comparedPattern($patterns, $source, &$offset)
    {
        if (!$patterns) {
            return null;
        };
        $g = new static;
        $g->m_matcher = $patterns;
        $tpos = $offset;
        return $g->detect($source, $tpos);
    }
    private function _treat(string $b, string &$o)
    {
        $o = '';
        if (preg_match(self::REGEX_OPTION, $b, $tab)) {
            $a = $tab['add'];
            $x = false;
            if ($a) {
                if (strpos($a, 'i') !== false) {
                    $o .= 'i';
                }
                if (strpos($a, 'm') !== false) {
                    $o .= 'm';
                }
                if (strpos('x', $a) !== false) {
                    $x = true;
                }
            }
            $a = igk_getv($tab, 'remove');
            if ($a) {
                if (strpos('i', $a) !== false) {
                    $o .= str_replace('i', '', $o);
                }
                if (strpos('m', $a) !== false) {
                    $o .= str_replace('m', '', $o);
                }
                if (strpos('x', $a) !== false) {
                    $x = false;
                }
            }
            // TODO : handle extra data
            // if($x){
            // extra data 
            //}
            $b = substr($b, strlen($tab[0]));
        }
        return $b;
    }
    /**
     * detecting regex type 
     */
    public function detect(string $source, int &$offset = 0)
    {
        if ($this->m_parent) {
            // 
            return $this->m_parent;
        }

        $m = $this->m_matcher;
        $result = [];
        $next_line = false;
        $_start_match = function (&$result, $b, $source, &$offset, $k) use (&$next_line) {
            $flag = PREG_OFFSET_CAPTURE;
            $o = '';
            if ($b) {
                $b = $this->_treat($b, $o);
                $b = sprintf("/%s/%s", $b, $o);
            }
            if ($b) {
                $cline = false;
                if (preg_match($b, $source, $tab, $flag, $offset)) {
                    $result[] = (object)[
                        'pos' => $tab[0][1],
                        'input' => $source,
                        'value' => $tab[0][0],
                        'match' => $k,
                        'captures' => $tab,
                        'parent' => null
                    ];
                } else if (($cline = preg_match(self::REGEX_START_LINE, $b)) && preg_match(
                    $b,
                    substr($source, $offset),
                    $tab,
                    $flag,
                    0
                )) {
                    //update data offset 
                    $keys = array_keys($tab);
                    while(count($keys)>0){
                        $q = array_shift($keys);
                        $tab[$q][1] += $offset; 
                    }
                    $result[] = (object)[
                        'pos' => $tab[0][1],
                        'input' => $source,
                        'value' => $tab[0][0],
                        'match' => $k,
                        'captures' => $tab,
                        'parent' => null
                    ];
                }

                $next_line = $next_line || $cline;
            }
        };
        $detect = true;
        $ln = strlen($source);
        while ($detect) {
            $detect = false;
            foreach ($m as $k) {
                switch ($k['type']) {
                    case 'begin/end':
                        $b = igk_getv($k, 'begin');
                        if ($b) {
                            $_start_match($result, $b, $source, $offset, $k);
                        }
                        break;
                    case 'match':
                        $b = igk_getv($k, 'match');
                        if ($b) {
                            $_start_match($result, $b, $source, $offset, $k);
                        }
                        break;
                }
            }
            // next line 
            $toffset = $offset< $ln ? strpos($source, "\n",  $offset) : false;

            if (count($result) > 0) {
                usort($result, function ($a, $b) {
                    return  $a->pos <=> $b->pos;
                });
                $r = $result[0];
                if ($next_line && ($toffset !== false))
                {
                    if ($r->pos > $toffset){
                        $detect = true;
                        $offset = $toffset+1;
                        $next_line = false;
                        continue;
                    }
                }
                $offset = $r->pos;
                return $r;
            }
            if ($next_line && ($toffset !== false)) {
                $offset = $toffset + 1;
                $detect = $ln > $offset;
               
                //restartd detection - move forward
            } else
                $offset = strlen($source);
            $next_line = false;
        }
    }
    /**
     * 
     * @param string $expression 
     * @param null|string $end 
     * @param null|string $tokenID 
     * @param null|string $refid 
     * @return $this 
     */
    public function begin(string $expression, ?string $end = null, ?string $tokenID = null, ?string $refid = null, ?array $patterns = null)
    {
        $inf =  Activator::CreateNewInstance(RegexMatcherPattern::class, [
            $this,
            'type' => 'begin/end',
            'begin' => $expression,
            'end' => $end,
            'tokenID' => $tokenID,
            'refid' => $refid,
            'patterns' => $patterns
        ]);
        if ($refid) {
            $this->m_references[$refid] = $inf;
        }
        $this->m_matcher[] = $inf;
        return $this;
    }
    // public function while(string $expression, ?string $end = null, $tokenID)
    // {
    //     $this->m_matcher[] = ['type' => 'begin/end', 'begin' => $expression, 'end' => $end, 'tokenID'=>$tokenID];
    // }
    public function match(string $expression, string $tokenID = null, ?string $refid = null)
    {
        $inf = Activator::CreateNewInstance(RegexMatcherPattern::class, [
            $this,
            'type' => 'match',
            'match' => $expression,
            'tokenID' => $tokenID,
            'refid' => $refid
        ]);
        if ($refid) {
            $this->m_references[$refid] = $inf;
        }

        $this->m_matcher[] = $inf;
        return $this;
    }

    /**
     * 
     * @param string $src source 
     * @param ?callable $filter callable {(string $g)=>boolean}
     * @return array 
     * @throws Exception 
     */
    public function extract(string $src, $filter = null, &$offset = 0)
    {
        $match = [];
        $pos = &$offset;
        while ($g = $this->detect($src, $pos)) {
            $g = $this->end($g, $src, $pos);
            if (!$filter || $filter($g))
                $match[] = $g->value;
        }
        return $match;
    }
    /**
     * treat and use callable
     * @param string $src 
     * @param callable $callable 
     * @return void 
     * @throws Exception 
     */
    public function treat(string $src, callable $callable, $end_token_id = '__end__')
    {
        $pos = 0;
        $skip = false;
        while ($g = $this->detect($src, $pos)) {
            $g = $this->end($g, $src, $pos);
            if (!$g || ($callable($g, $pos, $src) === true)) {
                $skip = true;
                break;
            }
        }
        if (!$skip) {
            $r = substr($src, $pos);
            if (strlen($r) > 0) {
                $callable((object)['tokenID' => $end_token_id, 'value' => $r, 'from' => $pos, 'to' => strlen($src)], $pos);
            }
        }
        $this->m_pos = $pos;
    }
    public function getLastPosition()
    {
        return $this->m_pos;
    }

    /**
     * append string detection 
     *  */
    public function appendStringDetection($tokenID = 'string')
    {
        $this->begin("(\"|')", "\\1", $tokenID);
        return $this;
    }
    /**
     * helper mark some definition 
     * @param string $mark 
     * @param string $tokenID 
     * @return $this 
     * @throws IGKException 
     */
    public function appendSingleLineComment($mark = '\/\/', $tokenID = 'single-comment')
    {
        $this->match($mark . ".+", $tokenID);
        return $this;
    }
    /**
     * append brank '()'
     * @param string $tokenId 
     * @return $this 
     * @throws IGKException 
     */
    public function appendBrank($tokenId = 'brank', $refid = null)
    {
        $this->begin('\(', '\)', $tokenId, $refid);
        return $this;
    }
    /**
     * append curly brank
     * @param string $tokenId 
     * @param mixed $refid 
     * @return $this 
     * @throws IGKException 
     */
    public function appendCurlyBrank($tokenId = 'curly-brank', $refid = null)
    {
        $this->begin('\{', '\}', $tokenId, $refid);
        return $this;
    }
    /**
     * append sqare brank
     * @param string $tokenId 
     * @param mixed $refid 
     * @return $this 
     * @throws IGKException 
     */
    public function appendSquareBrank($tokenId = 'square-brank', $refid = null)
    {
        $this->begin('\[', '\]', $tokenId, $refid);
        return $this;
    }
    public function appendCommentDocBlock($tokenId = 'comment-docbloc', $refid = null)
    {
        $this->begin('\/\*', '\*\/', $tokenId, $refid);
        return $this;
    }
}
