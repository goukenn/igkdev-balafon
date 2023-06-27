<?php
// @author: C.A.D. BONDJE DOUE
// @file: BindingExpressionReader.php
// @date: 20230517 14:33:31
namespace IGK\System\Templates;

use Closure;
use IGK\System\Console\Logger;
use IGK\System\DataArgs;
use IGK\System\Html\HtmlBindingRawTransform;
use IGK\System\Html\Templates\BindingContextInfo;

///<summary></summary>
/**
 * treat binding content
 * @package IGK\System\Templates
 */
class BindingExpressionReader
{
    var $startMarker = '{{';
    var $endMarker = '}}';
    var $escapedChar = "'";

    var $text;
    /**
     * get read value
     * @var ?string
     */
    var $value;
    /**
     * 
     * @var bool
     */
    var $escaped = false;

    /**
     * 
     * @var mixed
     */
    var $offset = 0;

    /**
     * mark data
     * @var array
     */
    var $mark = [];

    /**
     * transform to eval script content
     * @var bool
     */
    var $transformToEval;

    /**
     * skip mode 
     * @var bool
     */
    var $skipMode;

    /**
     * 
     * @var expression arg for skip mode 
     */
    var $expressionArgs;

    /**
     * expression args for name
     * @var mixed
     */
    var $expressionValueName;

    var $expressionTagName = IGK_ENGINE_EXPRESSION_NODE;

    /**
     * read marker 
     * @return bool 
     */
    public function read(): bool
    {
        $this->escaped = false;
        if (($pos = strpos($this->text, $this->startMarker, $this->offset)) !== false) {
            if ($pos > 0) {
                if ($this->text[$pos - 1] == $this->escapedChar) {
                    $this->escaped = true;
                }
            }
            $n = '';
            $tv = '';
            $offset = $pos + strlen($this->startMarker);
            $epos = false;
            $this->offset = $offset;
            while ($offset < strlen($this->text)) {
                $ch = $this->text[$offset];
                switch ($ch) {
                    case "'":
                    case "\"":
                        $tv .= igk_str_read_brank($this->text, $offset, $ch, $ch);
                        $ch = '';
                        break;
                }
                if ($ch  && (strrpos($tv . $ch, $this->endMarker) === strlen($tv) - 1)) {
                    $tv = substr($tv, 0, -1);
                    $epos = $offset - 1;
                    $offset++;
                    break;
                }
                $tv .= $ch;
                $offset++;
            }

            // $epos = strpos($this->text, $this->endMarker, $this->offset);
            // $npos = strpos($this->text, $this->startMarker, $this->offset);
            // $depth = 0;
            // while(($epos !== false) && ($npos !==false) ){           
            //     if ($npos < $epos) {
            //         // detect next 
            //         $poff = $npos + strlen($this->startMarker);   
            //         $npos = strpos($this->text, $this->startMarker, $poff);
            //         $depth++;                 
            //         continue;
            //     } else {
            //         while ($depth>0) {
            //             $epos = strpos($this->text, $this->endMarker, $epos+ strlen($this->endMarker));
            //             $depth--;
            //         }
            //         if ($epos<$npos){
            //             break;
            //         }
            //     }
            // }
            if ($epos !== false) {
                $n = substr($this->text, $this->offset, $epos - $this->offset);
                $this->offset = $epos +  strlen($this->endMarker);
                $this->mark = [substr($this->text, $pos, $this->offset - $pos), $pos, $this->offset];
            } else {
                $n = substr($this->text, $this->offset);
                $this->offset = $epos +  strlen($this->endMarker);
                $this->mark = [substr($this->text, $this->offset), $pos, strlen($this->text)];
            }
            $this->value = $n;
            return true;
        }
        return false;
    }
    /**
     * treat content 
     * @param string $content 
     * @param ?array|callable array of data or a callable $listener 
     * @return string 
     */
    public function treatContent(string $content, $listener = null)
    { 
        $v = '';
        $start = false;
        $loffset = 0;
        $reader = $this;
        $data = null;
        $this->text = $content;
        $this->offset = 0;
        if (is_null($listener) || !($listener instanceof Closure)) {
            $data = $this->_getBindingRawData($listener);
        
            $listener = function ($v) {
                extract(igk_extract_data(igk_getv(array_slice(func_get_args(), 1), 0) ?? ['raw' => new DataArgs([])]));
                $__c = $raw ;
                return @eval('return ' . $v . ';');
            };
        }

        while ($reader->read()) {
            if (!$start) {
                $start = true;
                if ($reader->escaped) {
                    $v .= substr($reader->text, $loffset, $reader->mark[1] - strlen($reader->escapedChar) - $loffset) . $reader->mark[0];
                    continue;
                } else{
                    $v .= substr($reader->text, 0, $reader->mark[1]);
                }
            }
            if ($reader->escaped) {
                $v .= substr($reader->text, $loffset, $reader->mark[1] - strlen($reader->escapedChar) - $loffset) . $reader->mark[0];
                continue;
            } else {
                if ($loffset > 0) {
                    $m = substr($reader->text, $loffset, $reader->mark[1] - $loffset);
                    // if ($this->stopOnTag && $m && preg_match("/<[^>]+>/", $m)){

                    //         break;                                                    
                    // }
                    $v .= $m;
                }
            }
            // + for piped value
            // igk_dev_wln(__FILE__ . ":" . __LINE__, "BEXP: " . $reader->value);
            $c = 0;
            if ($this->skipMode) {
                $this->expressionArgs[$this->expressionValueName] = str_replace("\"", "\\\"", 
                htmlentities($this->mark[0]));
                $dv = \igk_html_wtag($this->expressionTagName, "", $this->expressionArgs, 1);

            } else {
                if ($this->transformToEval) {
                    $dv = sprintf('<?= %s ?>', $reader->value);
                } else {
                    list($dv, $pipe) = igk_str_pipe_args($reader->value, $c);
                    $dv = $listener($dv, $data);
                    if ($pipe && $dv) {
                        $dv = igk_html_php_evallocalized_expression($dv, array_merge([
                            "v" => $dv,
                            "pipe" => $pipe
                        ], ($data ? ['raw' => $data['raw']] : null) ?? []));
                    }
                }
            }
            if (is_array($dv)) {
                $dv = json_encode($dv);
            }
            $v .= $dv;
            $loffset = $reader->offset;
        }
        $v .= substr($reader->text, $reader->offset);
        return $v;
    }
    private function _getBindingRawData($data)
    {
        if ($data instanceof BindingContextInfo) {
            $data = $data->to_array();// ["raw" => $data->to_array()];
        } else {
            if (is_object($data) && property_exists($data, 'raw')) {
                $data = $data;
            } else {
                $raw = igk_getv($data, 'raw') ?? [];
                $ctrl = igk_getv($data, 'ctrl');
                if (!is_array($data)) {
                    $data = ['raw' => new DataArgs($raw), 'ctrl'=>$ctrl];
                } else {
                    if ($raw instanceof HtmlBindingRawTransform){
                        $data = ['raw'=>$raw->data[$data['key']], 'ctrl'=>$ctrl];
                    } else {
                        $cdata = compact('raw', 'ctrl');
                        if (is_array($raw)){
                            $cdata = array_merge($cdata, $raw);
                        }
                        $cdata['raw'] = new DataArgs($cdata['raw']);
                        $data = $cdata;

                    }
                }
            }
        }
        return $data;
    }
}
