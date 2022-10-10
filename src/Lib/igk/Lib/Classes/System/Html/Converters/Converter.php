<?php
// @author: C.A.D. BONDJE DOUE
// @file: Converter.php
// @date: 20221006 10:33:53
namespace IGK\System\Html\Converters;

use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Dom\HtmlItemBase;
use IGKException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html\Converters
 */
class Converter
{
    var $ignoreEmpty = 1;
    var $tag = "notagnode";
    var $numeric_array_tag = "item";

    /**
     * convert to node
     * @param array|object $o object to convert
     * @return HtmlItemBase 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function Convert($o)
    {
        $tag = $this->tag;
        $ignoreEmpty = $this->ignoreEmpty;
        $numeric_array_tag = $this->numeric_array_tag;
        $n = igk_create_node($tag);
        $tab = [["n" => $n, "o" => $o]];
        while (count($tab) > 0) {
            $q = array_pop($tab);
            $t = $q["n"];
            $s = $q["o"];
            //igk_wln("the s ", $s);
            foreach ($s as $k => $v) {
                if ($ignoreEmpty && empty($v)) {
                    continue;
                }
                if ((is_numeric($k) && is_object($v) && ($v instanceof HtmlItemBase))) {
                    $t->add($v);
                    continue;
                }
                if (is_numeric($k)) {
                    $k = $numeric_array_tag;
                }
                $vn = igk_create_xmlnode($k);
                $t->add($vn);
                if (is_object($v) || is_array($v)) {
                    if ($v instanceof HtmlItemBase) {
                        $vn->add($v);
                        continue;
                    }
                    if ($v instanceof HtmlConvDefinition) {
                        $vn->setAttributes($v->attr);
                        $v = $v->value;
                        if (!is_array($v)) {
                            $v = [$v];
                        }
                    }
                    array_push($tab, ["n" => $vn, "o" => $v]);
                } else {
                    $vn->Content = $v;
                }
            }
        }
        return $n;
    }
}
