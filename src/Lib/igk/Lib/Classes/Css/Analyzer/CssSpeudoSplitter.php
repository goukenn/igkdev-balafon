<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssSpeudoSplitter.php
// @date: 20250627 06:18:41
namespace IGK\Css\Analyzer;
use IGK\System\Text\RegexMatcherContainer;
/**
 * 
 * @package IGK\Css\Analyzer
 * @author C.A.D. BONDJE DOUE
 */
class CssSpeudoSplitter implements ICssSplitListener
{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_rg;

    /**
    * .ctr
    */
    public function __construct()
    {
        $this->m_rg = new RegexMatcherContainer;
        $this->initialize($this->m_rg);
    }

    /**
    * auto generate doc.
    * @param mixed $rg
    */
    protected function initialize($rg)
    {
        $rg->appendStringDetection();
        $rg->match(',', 'split');
    }

    /**
    * auto generate doc.
    * @param string $value
    * @return array
    */
    public function split(string $value): array
    {
        $pos = 0;
        $o = [];
        $offset = 0;
        while ($g = $this->m_rg->detect($value, $pos)) {
            if ($e = $this->m_rg->end($g, $value, $pos)) {
                if ($e->tokenID == 'split') {
                    if ($l = trim(substr($value, $offset, $e->from - $offset)))
                        $o[] = $l;
                    $offset = $e->to;
                }
            }
        }
        if ($to = trim(substr($value, $offset))) {
            $o[] = $to;
        }
        $this->m_rg->resetTreatment();
        return $o;
    }
}