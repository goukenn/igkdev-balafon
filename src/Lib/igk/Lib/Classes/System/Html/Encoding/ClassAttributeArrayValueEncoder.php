<?php
// @author: C.A.D. BONDJE DOUE
// @file: ClassAttributeArrayValueEncoder.php
// @date: 20230316 11:14:17
namespace IGK\System\Html\Encoding;


///<summary></summary>
/**
 * 
 * @package IGK\System\Html\Encoding
 */
class ClassAttributeArrayValueEncoder extends AttributeEncoder
{
    var $char_list = [
        "&quot;" => "'",
    ];
    /**
     * 
     * @var false
     */
    var $strip_expression = false;

    private $m_litteral_start;

    public static function DetectArrayList(string $value)
    {
        return $value && preg_match("/^\[[^\]]+\]$/", trim($value));
    }
    /**
     * encode entity string to expression array litteral
     * @param string $g 
     * @return string 
     */
    public function encode(string $g): string
    {
        $v = "";
        $out = "";
        $ln = strlen($g);
        $pos = 0;
        $depth = 0;
        $start = 0;
        $lpos = 0;
        $express_start = 0;
        $litteral_start = &$this->m_litteral_start; // use to dected expression ....
        $litteral_start = 0;
        $func_callback = function ($value) {
            return parent::decode($value);
        };
        while ($pos < $ln) {
            $ch = $g[$pos];
            switch ($ch) {
                case '[':
                    $depth++;
                    $litteral_start = $pos;
                    break;
                case ']':
                    $depth--;
                    $this->_appendOutput($func_callback, $out, $v, $lpos, false, $this->strip_expression, $express_start);
                    $out .= $ch;
                    $ch = '';
                    $v = "";
                    $start = 0;
                    $lpos = 0;
                    break;
                case ',';
                    $this->_appendOutput($func_callback, $out, $v, $lpos, false, $this->strip_expression, $express_start);
                    $v = "";
                    $start = 0;
                    $lpos = 0;
                    break;
                case '&':
                    if (preg_match("/&[^\\s]+;/", $g, $tab, 0, $pos)) {
                        $n_pos = strpos($g, ';', $pos);
                        $n = substr($g, $pos, $n_pos + 1 - $pos);
                        switch ($n) {
                            case '&quot;':
                                if (!$start) {
                                    $lf = trim(substr($g, $litteral_start + 1, $pos - $litteral_start - 1));
                                    $v .= "'";
                                    $start = empty($lf);
                                    $out .= $v;
                                    $v = "";
                                    $express_start = strlen($out);
                                } else {
                                    $lpos = strlen($v);
                                    $v .= $n;
                                }
                                break;
                            case '&#039;':
                                $out.=$v."\\'";
                                $v = "";
                                $ch = '';
                                $start = false;
                                break;
                            // + | litteral symbole 
                            case '&gt;':
                            case '&lt';
                                $v.= html_entity_decode($n);
                                $ch = "";
                                break;
                            default:
                                $lpos = strlen($v);
                                $v .= $n;
                                break;
                        }
                        $ch = '';
                        $pos = $n_pos;
                    }
                    break;
                default:
                    # code...
                    break;
            }
            $pos++;

          
            if ($lpos && !empty($ch)) {
                $this->_appendOutput($func_callback, $out, $v, $lpos, $start, $this->strip_expression, $express_start);
                $lpos = 0;
                $v = "";
            } 
            $v .= $ch;
        }
        if ($v) {
            $this->_appendOutput($func_callback, $out, $v, $lpos, false, $this->strip_expression, $express_start);
        }
        return $out;
    }
    private function _appendOutput($func_callback, &$out, $v, $lpos, $start = false, $strip_expression = false, $express_start = 0)
    {
        if ($lpos) {
            $v = substr($v, 0, $lpos);
        }
        $v = $func_callback($v);
        $out .= $v;
        if ($lpos) {
            $out .= $start ? '"' : "'";
            if (!$start && $strip_expression) {
                $out = substr($out, 0, $express_start - 1) .
                    igk_str_strip_surround(substr($out, $express_start - 1));
            }
        }
    }
}
