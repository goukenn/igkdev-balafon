<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherUtility.php
// @date: 20241031 17:45:12
namespace IGK\System\Text;

use IGKException;
use Exception;

///<summary></summary>
/**
* regex utility method
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
abstract class RegexMatcherUtility{
    /**
     * 
     * @param RegexMatcherContainer $ctn the container
     * @param string $haystack the string to operate 
     * @return (string|array)[] 
     * @throws Exception 
     */
    public static function TreatByRemoveRootScopePattern(RegexMatcherContainer $ctn, string $haystack){
        $ch = '';
        $npos = 0;
        $def = [];
        $ctn->treat($haystack, function ($g, $next_pos, $data) use (&$npos, &$ch, &$def) {
            if ($g->parentInfo == null) {
                RegexMatcherUtility::Skip($g, $next_pos, $data, $npos, $ch);
                $def[] = $g->value;
            }
        });
        $ch .= substr($haystack, $npos);
        $sb = $ch;
        return [$sb, $def];
    }
    /**
     * create a parameter reference
     * @param string $begin 
     * @param string $end 
     * @return RegexMatcherContainer 
     * @throws IGKException 
     * @throws Exception 
     */
    public static function ParameterReference($begin='\(', $end='\)'){
        $ctn = new RegexMatcherContainer;
        $param_block = $ctn->begin($begin, $end, 'parameter')->last(); 
        $param_block->patterns = [
            $param_block 
        ]; 
        return $ctn;
    }
    public static function ExtractFirst(string $match, RegexMatcherContainer $ref, & $pos){  
        $v = '';
        $ref->treat($match, function($g, $next_pos)use(& $v, & $pos){
            if (!$g->parentInfo){
                $v = $g->value;
                $pos = $next_pos; 
                return true;
            }
        });
        return $v;
    }

    public static function CodeCommentMatcherReference(){
        $ctn = new RegexMatcherContainer;
        $ctn->match('\/\/.+', 'single-line'); 
        $ctn->begin('\/\*', '\*\/', 'multiline')->last();  
        return $ctn;
    }

    public static function RemoveComment(string $match){
        $cnf = self::CodeCommentMatcherReference();
        $v = '';
        $pos = 0;
        $cnf->treat($match, function($g, $next_pos, $data)use(& $v, & $pos){
            $v .= rtrim(substr($data, $pos, $g->from-$pos));
            $pos = $next_pos; 
        });
        $v .= substr($match, $pos);
        return $v;

    }
    /**
     * skip value 
     * @param mixed $g 
     * @param mixed $next_pos 
     * @param mixed $data 
     * @param mixed &$pos 
     * @param mixed &$ch 
     * @return void 
     */
    public static function Skip($g, $next_pos, $data, & $pos, & $ch ){
        $ch .= rtrim(substr($data, $pos, $g->from-$pos));
        $pos = $next_pos;
    }
}