<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSTreatment.php
// @date: 20241020 18:28:14
namespace IGK\Helper;

use IGK\System\Text\RegexMatcherContainer;

///<summary></summary>
/**
* 
* @package IGK\Helper
* @author C.A.D. BONDJE DOUE
*/
class JSTreatment{
    public static function RemoveOutsideSymbol(string $t){
        $rgex = new RegexMatcherContainer;
        $rgex->begin("(\"|')", "\\1","string");
        $rgex->match('\\\(n|r)', "symbol");
        $pos = 0;
     
        while($g = $rgex->detect($t, $pos)){
            $rpos = $pos;
            $g = $rgex->end($g, $t, $pos);
            $token_id = $g->tokenID;
            switch($token_id){
                case 'symbol':
                    //remove symobl
                    $t = substr($t, 0, $rpos).substr($t, $pos);
                    $pos = $rpos;
                    break;
            }
        }
        return $t;
    }
}