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
class CssMinifier{
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
    public function minify(string $css){ 
        $container = new RegexMatcherContainer;
        $container->match("\\s+(\/|\+|-|%|\*)\\s+", 'operator'); // ignore multispace 
        $container->match("\\s+", 'skip');
        $container->begin("(\"|')", '\\1'); // ignore multispace 
        $container->begin("\/\*", '\*\/', 'comment'); // ignore multispace 
        $lpos = 0;
        $ch = '';
        $q = $this;
        $container->treat($css, function($g, $pos, $data) use( & $ch, & $lpos, $q){
            //igk_wln($g->tokenID);
            switch($g->tokenID){ 
                case 'comment':
                    if ($q->preserveComment){
                        $ch .= $g->value; 
                    } 
                    break;
                case 'operator':
                    $ch .= substr($data, $lpos, $g->from-$lpos).sprintf(' %s ',trim($g->value));
                    break;
                default:
                    $ch.= substr($data, $lpos, $g->from-$lpos); 
                break;
            }
            $lpos = $pos;
        });
        $ch .= substr($css, $lpos);
        return $ch;
    }

}