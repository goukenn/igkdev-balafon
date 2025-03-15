<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssMinifier.php
// @date: 20241029 14:17:00
namespace IGK\System\Html\Css;

use Exception;
use IGK\System\Text\RegexMatcherContainer;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html\Css
 * @author C.A.D. BONDJE DOUE
 */
class CssMinifier
{
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
        $patterns[] = $container->match("\\s+(\/|\+|-|%|\*)\\s+", 'operator')->last(); // ignore multispace 
        $patterns[] = $container->match("\\s*(:|;|,|\(|\))\\s*", 'glue')->last(); // ignore multispace 
        $patterns[] = $container->match("\\s+", 'skip')->last();
        $patterns[] = $container->begin("(\"|')", '\\1')->last(); // ignore multispace 
        $patterns[] = $container->begin("\/\*", '\*\/', 'comment')->last(); // ignore multispace 
        $g = $container->begin('{', '}', 'block')->last();
        $g->patterns = $patterns;
        $lpos = 0;
        $ch = '';
        $q = $this;

        $container->treat($css, function ($g, int $pos, $data) use (&$ch, &$lpos, $q) {
          //  igk_debug_wln($g->tokenID);
            if (is_null($g->parentInfo)) {
                switch ($g->tokenID){
                    case 'comment':
                        if ($q->preserveComment) {
                            $ch .= $g->value;
                        }
                        break;
                    case 'operator':
                        $ch .= substr($data, $lpos, $g->from - $lpos) . sprintf(' %s ', trim($g->value));
                        break;
                    case 'skip': 
                        // just copy 
                        $tc = substr($data, $lpos, $g->from - $lpos) . ' ';
                        if (!empty(trim($tc)))
                            $ch .= $tc;
                        break;
                    default:
                        $ch .= substr($data, $lpos, $g->from - $lpos) . $g->value;
                        break;
                }
                $lpos = $pos;
            } else {
                switch ($g->tokenID) {
                    case 'skip':
                        $g->value = ' ';
                        break;
                    case 'glue':
                        $g->value = trim($g->value);
                        break;
                    default:
                        $g->value = '';
                        break;
                }
                $p = $g->parentInfo;
                $ns = substr($data, $lpos, $g->from - $lpos).$g->value;
                $ch.= $ns;
                $lpos = $g->to;
                $p->pos = $g->to;
            }
        });
        $ch .= substr($css, $lpos);
        return $ch;
    }
}
