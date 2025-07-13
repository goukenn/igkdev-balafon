<?php
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherParentChainReplacement;
if (!function_exists('igk_dump_export')) {
    /**
     * 
     * @param string $m 
     * @return void 
     */
    function igk_dump_export($data)
    {
        $m = var_export($data, true);
        $remove_export_array = function ($src) {
            $ss = substr($src, 0, -1) . ']';
            $ss = '[' . substr($ss, strpos($ss, '(') + 1);
            return $ss;
        };
        // transform to som litteral 
        $c = new RegexMatcherContainer;
        $replace = new RegexMatcherParentChainReplacement;
        $c->autoStore = false;
        $str = $c->appendStringDetection()->last();
        $skip = $c->match('\\d+\\b\\s*=>', 'skip-number')->last();
        $skip = $c->match('\\s+', 'skip-multispace')->last();
        $c->autoStore = true;
        $tc = $c->begin('\\barray\\b\\s*\\(', '\\)', 'array_block')->last();
        $tc->patterns = [$str, $skip, $tc];
        $pos = 0;
        $out = '';
        while ($g = $c->detect($m, $pos)) {
            if ($e = $c->end($g, $m, $pos)) {
                switch ($e->tokenID) {
                    case 'array_block':
                        $ss = $replace->replaceChain($g, $e->value, $e->from);
                        $ss = $remove_export_array($ss);
                        if ($e->parentInfo == null) {
                            $out .= $ss;
                            // stransform
                        } else {
                            $replace->mark($ss, $e);
                        }
                        break;
                    case 'skip-number':
                        $replace->mark("", $e);
                        break;
                    case 'skip-multispace':
                        $replace->mark(" ", $e);
                        break;
                }
            }
        }
    }
}