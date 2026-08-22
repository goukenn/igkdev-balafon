<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormUtils.php
// @date: 20221111 14:05:40
namespace IGK\System\Html\Forms;

use ArrayAccess;
use IGK\Helper\ArticleContentBindingHelper;
use IGKException;

/**
* auto generate doc.
* @package IGK\System\Html\Forms
*/
class FormUtils
{
    /**
     * build select data
     * @param mixed $list 
     * @param string $key key used for the value : i
     * @param string|callable $display key used for display: t
     * @param mixed $options 
     * @return array 
     * @throws IGKException 
     */    
    public static function SelectData($list, string $key, ?string $display=null, $options = null)
    {
        $display = $display ?? $key;
        $selected = $options ? igk_getv($options, 'selected') : null;
        $callback = $options ? igk_getv($options, 'callback') : null;
        $empty = $options ? igk_getv($options, 'empty') : null;
        $offset = $options ? igk_getv($options, 'offset') : 0;
        $data = [];
        if ($list)
        foreach ($list as $m) {
            if ($callback) {
                if ($g = $callback($m)) {
                    $data[] = $g;
                }
                continue;
            }
            $text = '';
            if (is_callable($display)){
                $text = $display($m);
            } else if (is_string($display)){
                if (property_exists($m, $display) || isset($m->$display) || (($m instanceof ArrayAccess) && isset($m[$display]))){
                    $text = igk_getv($m, $display);
                }else {
                    $g = ArticleContentBindingHelper::GetData($m); 
                    if (key_exists($display, $g)){
                        $text = $g[$display];
                    } else{
                        $text = ArticleContentBindingHelper::BindContent($display, $g );
                    }
                }
            }
            $g = ["i" => $key ? $m->{$key} : count($data) + $offset, "t" =>  $text];
            if ((is_callable($selected) && $selected($m)) || ($selected && ($selected == $g["i"]))) {
                $g["selected"] = true;
            }
            $data[] = $g;
        }
        if (!igk_getv($options, 'no_sort_text')){
            usort($data, function($a, $b){
                return strcasecmp($a['t'], $b['t']);
            });
        }
        if ($empty){
            array_unshift($data, ['i'=>igk_getv($empty, 'value', -1), 't'=> igk_getv($empty, 'text', '---')]);
        }
        return $data;
    }
}