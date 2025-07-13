<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssMinifier.php
// @date: 20241029 14:17:00
namespace IGK\System\Html\Css;
use Exception;
use IGK\System\Console\Logger;
use IGK\System\Text\RegexMatcherContainer;
/**
 * 
 * @package IGK\System\Html\Css
 * @author C.A.D. BONDJE DOUE
 */
class CssMinifier
{
    const CSS_PROPS = "\\b(?:--|[a-zA-Z]+)[a-zA-Z\-0-9]*\\b";
    const CSS_PROVIDER_PROPS = "-(webkit|moz|ms|o)-[a-zA-Z\-0-9]+\\b";
    /**
     * leave comment
     * @var ?bool
     */
    var $preserveComment;
    private $m_container;
    protected function getRegexContainer(){
        if ($this->m_container){
            return $this->m_container;
        }
        $container = $this->m_container  = new RegexMatcherContainer;
        $patterns = [];
        $patterns[] = $container->begin("\\s*\/\*", '\*\/\\s*', 'comment')->last(); // ignore comment 
        $patterns[] = $container->match(':(active|any-link|autofill|blank|checked|current|default|defined|dir|disabled|empty|enabled|first(-(child|of-type))?|focus|focus-visible|focus-within|fullscreen|future|has|host|host|host-context|hover|indeterminate|in-range|invalid|is|lang|last-child|last-of-type|left|link|local-link|modal|not|nth-child|nth-last-child|nth-last-of-type|nth-of-type|only-child|only-of-type|optional|out-of-range|past|paused|picture-in-picture|placeholder-shown|playing|read-only|read-write|required|right|root|scope|state|target|target-within|user-invalid|valid|visited|where)\\b', 'speudo-class')->last(); // 
        $patterns[] = $container->match('\\s*\\b(and|or|not|only)\\b\\s*', 'operator-litteral')->last(); // 
        $patterns[] = $container->match('\\s*\\b(var|min|max|linear-gradient|color|translate(X|Y)?|scale|rotate|rgb(a)?|hsl|calc)\\b(\\s*)(?=\\()', 'method-name')->last(); // 
        $patterns[] = $container->match(self::CSS_PROVIDER_PROPS, 'property')->last(); // 
        $patterns[] = $container->match(self::CSS_PROPS, 'property')->last(); // 
        // priority to skip space
        $patterns[] = $container->match("\\s+(?=\\}|\\{)", 'skip-space')->last();
        $patterns[] = $container->match("\\s*(:|;|,|\(|\)|\[|\])\\s*", 'glue')->last(); // 
        $patterns[] = $container->match("\\s*(~|\*|\+|\!)=\\s*", 'glue-operator')->last(); // glue operator - remove space
        $patterns[] = $container->match("\\s+", 'skip')->last();
        $patterns[] = $container->match('\\s*(?:-)?(?:([0-9]+)?\.)?([0-9]+)(px|em|%|s|ms|rem|pt|pica|vh|vw|deg|rad|grad|ch)?', 'dimension')->last(); // 
        $patterns[] = $container->match('(?i)\\s*--[a-z\\-]+\\b', 'litteral-property')->last(); // 
        $patterns[] = $container->match("\\s*(\/|\+|-|%|\*|>|~)\\s*", 'operator')->last(); // ignore multispace 
        $patterns[] = $container->begin("(\"|')", '\\1', 'string-litteral')->last(); 
        $g = $container->begin('{', '}', 'block')->last();
        $patterns[] = $g;
        $g->patterns = $patterns;
        return $container;
    }
    /**
     * 
     * @param string $css 
     * @return string 
     * @throws Exception 
     */
    public function minify(string $css)
    {        
        $container = $this->getRegexContainer(); 
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
                    case 'litteral-property':
                        $g->value = sprintf('%s', trim($g->value));
                        $glueInf[] = 1;
                        break;
                    case 'method-name':
                        $g->value = sprintf('%s', trim($g->value));
                        break;
                    case 'property':
                    case 'dimension':
                    case 'block': // block for child
                    case 'speudo-class':
                        // + | just load data 
                        break;
                    case 'string-litteral':
                        // + | 
                        $g->value = sprintf("'%s'", igk_str_remove_quote($g->value));
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