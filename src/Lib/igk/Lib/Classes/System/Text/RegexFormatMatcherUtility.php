<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexFormatMatcherUtility.php
// @date: 20250712 12:52:06
namespace IGK\System\Text;

use IGK\System\Console\Logger;

/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
class RegexFormatMatcherUtility{

    /**
    * auto generate doc.
    * @param string $value
    * @param mixed $chain
    * @param int $from
    * @param RegexFormatStringBuilder $builder
    */
    public static function ReplaceChain(string $value, $chain, int $from, RegexFormatStringBuilder $builder ){
        $Tss = $value;
        $offset = $from;
        $v_fsb = $builder; 
        $k = 0;
        $v_d = $builder->tab();
        while (count($chain) > 0) {
            $q = array_shift($chain);
            list($tc, $ss) = $q;           
            $v_size = ($tc->from - $offset)-$k;
            if ($v_size<0){
                igk_die("invalid chain detection");
            }
            $before = substr($Tss, $k, $v_size);// - $offset) - $k);
            //format previous data
            $tr = explode("\n", $before);
            if (count($tr) > 1) {
                $before = RegexFormatStringBuilder::ClueDef($before, $v_d); 
            }
            // $r .= $before . $ss;
            igk_is_debug() &&  Logger::danger("before:[" . $before."]");
            $v_fsb->append($before);
            $v_fsb->lineFeed = $v_fsb->lineFeed || igk_str_endwith($before, "\n");
            if (igk_getv($q, "rtrim")){
                 $v_fsb->rtrim();
                 $v_fsb->lineFeed = false; 
            }
            $v_fsb->append($ss);
            $v_fsb->lineFeed = $v_fsb->lineFeed || igk_str_endwith($ss, "\n") || preg_match("/\}$/", rtrim($ss));
            $k = $tc->to - $offset;       
        }
        $v_fsb->append(substr($Tss, $k));
        return $v_fsb.'';
    }
}