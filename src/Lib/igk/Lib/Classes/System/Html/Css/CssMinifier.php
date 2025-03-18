<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssMinifier.php
// @date: 20241029 14:17:00
namespace IGK\System\Html\Css;

use Exception;
use IGK\System\Console\Logger;
use IGK\System\Text\RegexMatcherContainer;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html\Css
 * @author C.A.D. BONDJE DOUE
 */
class CssMinifier
{
    const CSS_PROPS = "\\b(?:--|[a-zA-Z]+)[a-zA-Z\-0-9]*\\b";
    /**
     * leave comment
     * @var ?bool
     */
    var $preserveComment;
    /**
     * 
     * @param string $css 
     * @return string 
     * @throws Exception 
     */
    public function minify(string $css)
    {        
        $container = new RegexMatcherContainer;
        $patterns = [];
        $patterns[] = $container->begin("\\s*\/\*", '\*\/\\s*', 'comment')->last(); // ignore comment 
        $patterns[] = $container->match('\\s*\\b(and|or)\\b\\s*', 'operator-litteral')->last(); // 
        $patterns[] = $container->match(self::CSS_PROPS, 'property')->last(); // 
        // priority to skip space
        $patterns[] = $container->match("\\s+(?=\\}|\\{)", 'skip-space')->last();
        $patterns[] = $container->match("\\s*(:|;|,|\(|\)|\[|\])\\s*", 'glue')->last(); // 
        $patterns[] = $container->match("\\s*(~|\*|\+|\!)=\\s*", 'glue-operator')->last(); // glue operator - remove space
        $patterns[] = $container->match("\\s+", 'skip')->last();
        $patterns[] = $container->match('\\s*(?:-)?(?:([0-9]+)?\.)?([0-9]+)(px|em|%|s|ms|rem|pt|pica|vh|vw|deg|rad|grad)?', 'dimension')->last(); // 
        $patterns[] = $container->match("\\s*(\/|\+|-|%|\*|>|~)\\s*", 'operator')->last(); // ignore multispace 
        $patterns[] = $container->begin("(\"|')", '\\1')->last(); 
        $g = $container->begin('{', '}', 'block')->last();

        $patterns[] = $g;
        $g->patterns = $patterns;
        $lpos = 0;
        $ch = '';
        $q = $this;
        $glueInf = [];
        $container->treat($css, function ($g, int $pos, $data) use (&$ch, &$lpos, $q, & $glueInf) {
            igk_debug_wln($g->tokenID . ' : '.$g->value);
            $v_tp = array_pop($glueInf);
            if (is_null($g->parentInfo)) {
                switch ($g->tokenID){
                    case 'comment':
                        if ($q->preserveComment) {
                            $ch .= $g->value;
                        }
                        break;
                    case 'operator':
                    case 'operator-litteral':
                        $ch .= substr($data, $lpos, $g->from - $lpos) . sprintf(' %s ', trim($g->value));
                        //if ($g->tokenID != 'operator-litteral')
                        $glueInf[] = 1; 
                        break;
                    case 'glue':
                    case 'glue-operator':
                        $ts = trim($g->value);
                        $ch = rtrim($ch.substr($data, $lpos, $g->from - $lpos));
                        if ($v_tp) $ch.= ' ';
                        $ch.= trim($g->value); 
                        break;
                    case 'skip': 
                        // -----------------------------------------------------------------------------------------
                        // just copy
                        // 
                        $tc = substr($data, $lpos, $g->from - $lpos) . ' ';
                        if (!empty($ch) || !empty(trim($tc)))
                        { $ch .= $tc; $glueInf[]= 1;}            
                        break;
                    default:
                        $vv = substr($data, $lpos,$ln = ($g->from - $lpos));
                        if ((!$v_tp) && ($ln==0)){
                            $ch = rtrim($ch);
                        }
                        $ch .= $vv . $g->value;
                        break;
                }
                $lpos = $pos;
            } else {
                $p = $g->parentInfo;
                switch ($g->tokenID) {
                    case 'skip-space':
                        $g->value = '';
                        break;
                    case 'skip':
                        $ch = !$v_tp ? rtrim($ch): $ch;
                        $g->value = isset($p->start) ? ' ' : '';
                        $glueInf[] = 1;
                        break;
                    case 'glue':
                        $ch = rtrim($ch);
                        $g->value = trim($g->value);
                        break;
                    case 'operator':
                        $ch = rtrim($ch);
                        $g->value = sprintf(' %s ', trim($g->value));
                        $glueInf[] = 1;
                        break;
                    case 'property':
                    case 'dimension':
                    case 'block': // block for child
                        //$ch = rtrim($ch);
                        break;
                    default:
                        $g->value = '';
                        break;
                }
                $ns = substr($data, $lpos, $g->from - $lpos).$g->value;
                if (!$v_tp && (($g->from - $lpos) == 0)){
                    $ch = rtrim($ch);
                } 
                $ch.= $ns;
                $lpos = $g->to;
                $p->pos = $g->to;
                $p->start= 1;
            }
        });
        $ch .= substr($css, $lpos);
        return $ch;
    }
}
