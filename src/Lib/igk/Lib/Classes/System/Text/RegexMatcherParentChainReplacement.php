<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherParentChainReplacement.php
// @date: 20250617 12:49:56
namespace IGK\System\Text;
/**
* auto generate doc.
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
class RegexMatcherParentChainReplacement
{
    /**
    * Property: chain parent.
    * @var mixed
    */
    private $m_chain_parent;
    /**
    * .ctr
    */
    public function __construct()
    {
        $this->m_chain_parent = [];
    }
    /**
     * mark chain
     * @param string $value 
     * @param IRegexEndInfo $e 
     * @return void 
     */
    public function mark(string $value, $e)
    {
        $cp = null;
        $chain_parent = &$this->m_chain_parent;
        if (($new = (!$chain_parent || (($cp = $chain_parent[0]['p']) !== $e->parentInfo))) || $cp) {
            $cr = (object)[
                'value' => $value,
                'from' => $e->from,
                'to' => $e->to,
                'token' => $e->tokenID
            ];
            if (!$new && $cp) {
                $chain_parent[0]['l'][] = $cr;
            } else {
                array_unshift($chain_parent, [
                    'p' => $e->parentInfo,
                    'l' => [$cr]
                ]);
            }
        }
    }
    /**
    * Replaces Chain.
    * @param mixed $g
    * @param string $value
    * @param int $from
    */
    public function replaceChain($g, string $value, int $from)
    {
        $chain_parent = &$this->m_chain_parent;
        if ($chain_parent) {
            $fip = $chain_parent[0];
            if ($fip['p'] === $g) {
                $ss = '';
                $rc = $value;
                $moff = $off = $from;
                while (count($fip['l']) > 0) {
                    $q = array_shift($fip['l']);
                    $ss .= substr($rc, $moff - $off, $q->from - $moff);
                    $ss .= $q->value;
                    $moff = $q->to;
                }
                $ss .= substr($rc, $moff - $off);
                array_shift($chain_parent);
                return $ss;
            }
        }
        return $value;
    }
}