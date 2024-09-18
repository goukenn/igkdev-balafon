<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherContainer.php
// @date: 20240913 10:19:11
namespace IGK\System\Text;

use Exception;

///<summary></summary>
/**
 * extract definitio beetween begin/end definition 
 * @package IGK\System\Text
 * @author C.A.D. BONDJE DOUE
 */
class RegexMatcherContainer
{
    private $m_matcher = [];
    /**
     * end information 
     * @param mixed $info 
     * @param mixed $source 
     * @param int &$offset 
     * @return object|void 
     * @throws Exception 
     */
    public function end($info, $source, int &$offset = 0)
    { 
        $k = $info->match;
        switch ($k['type']) {
            case 'begin/end':
                $b = igk_getv($k, 'end');
                $o = '';
                if ($b) {
                    $b = $b ? sprintf("/%s/%s", $b, $o) : null;
                }
                $b = preg_replace_callback("/\\\\(?P<id>\d+)/", function($m)use($info){
                    $id = intval($m['id']);
                    return $info->captures[$id][0];
                }, $b );
                $offset+= strlen($info->value);

                if (preg_match($b, $source, $tab, PREG_OFFSET_CAPTURE, $offset)) {
                    $n = $tab[0][1] + strlen($tab[0][0]);
                    $offset = $n;
                    return (object)[
                        'tokenID' => $k['tokenID'],
                        'from'=>$info->pos,
                        'to'=>$n,
                        'value'=>substr($source, $info->pos, $n - $info->pos)
                    ];
                }
                break;
            case 'match':
                $n = $info->pos + strlen($info->value);
                $offset = $n;
                return (object)[
                    'tokenID' => $k['tokenID'],
                    'from'=>$info->pos,
                    'to'=>$n,
                    'value'=>substr($source, $info->pos, $n - $info->pos)
                ];
                break;
        }
    }
    public function detect($source, int &$offset = 0)
    {
        $m = $this->m_matcher;
        $result = [];
        $_start_match = function(& $result, $b, $source, & $offset, $k){
            $o = '';
            $b = $b ? sprintf("/%s/%s", $b, $o) : null;
            if (preg_match($b, $source, $tab, PREG_OFFSET_CAPTURE, $offset)) {
                $result[] = (object)[
                    'pos' => $tab[0][1],
                    'input' => $source,
                    'value' => $tab[0][0],
                    'match' => $k,
                    'captures'=>$tab
                ];
            }
        };
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
                    if ($b){
                        $_start_match($result, $b, $source, $offset, $k);
                    }
                    break;
            }
        }
        if (count($result) > 0) {
            usort($result, function ($a, $b) {
                return  $a->pos <=> $b->pos;
            });
            $r = $result[0];
            $offset = $r->pos;
            return $r;
        }
        $offset = strlen($source);
    }
    public function begin(string $expression, ?string $end = null, ?string $tokenID = null)
    {
        $this->m_matcher[] = ['type' => 'begin/end', 'begin' => $expression, 'end' => $end, 'tokenID'=>$tokenID];
    }
    // public function while(string $expression, ?string $end = null, $tokenID)
    // {
    //     $this->m_matcher[] = ['type' => 'begin/end', 'begin' => $expression, 'end' => $end, 'tokenID'=>$tokenID];
    // }
    public function match(string $expression, string $tokenID=null)
    {
        $this->m_matcher[] = ['type' => 'match', 'match' => $expression, 'tokenID'=>$tokenID];
    }

    /**
     * 
     * @param string $src source 
     * @param ?callable $filter callable {(string $g)=>boolean}
     * @return array 
     * @throws Exception 
     */
    public function extract(string $src, $filter=null, & $offset=0){
        $match = [];
        $pos = & $offset;
        while ($g = $this->detect($src, $pos)) {  
            $g = $this->end($g, $src, $pos);
            if (!$filter || $filter($g))
                $match[] = $g->value; 
        }
        return $match;
    }
}
