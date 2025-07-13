<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexDetectBuffer.php
// @date: 20250702 12:29:43
namespace IGK\System\Text;

use Exception;

/**
 * 
 * @package IGK\System\Text
 * @author C.A.D. BONDJE DOUE
 */
class RegexDetectBuffer
{
    var $output = '';
    var $offset = 0;
    var $source;
    var $depth;
    var $lineFeed = false;
    var $replace = [];

    private $m_initFormatBuilderListener;
    /**
     * 
     * @param null|callable $init 
     * @return void 
     */
    public function setinitFormatBuilderListener(?callable $init){
        $this->m_initFormatBuilderListener = $init;
    }
    /**
     * 
     * @return bool 
     */
    public function isEmpty()
    {
        return empty(trim($this->output));
    }
    function replace($e, string $value = '')
    {
        $v_before = substr($this->source, $this->offset, $e->from - $this->offset);
        if ($this->lineFeed) {
            $v_before = ltrim($v_before);
            $this->lineFeed = false;
        }
        $this->output .= $v_before . $value;
        $this->offset = $e->to;
        $this->lineFeed = $this->lineFeed || $this->checkLineFeed($value);
    }
    public function checkLineFeed(string $v)
    {
        return preg_match("/\}$/", $v);
    }
    function end()
    {
        $this->output .= substr($this->source, $this->offset);
    }
    function output()
    {
        $this->end();
        return $this->output;
    }
    function rtrim(){
        $this->output = rtrim($this->output);
    }
    public function initFormatBuilder(){
        if ($fc = $this->m_initFormatBuilderListener){
            return $fc() ?? igk_die('failed to initialize listener');
        }
    }
    /**
     * replace all with the express 
     * @param array $infos 
     * @param string $value 
     * @param string $expression 
     * @param int $from 
     * @return void 
     */
    public function replaceAll(array $infos, string $value, string $expression, int $from = 0): string
    {
        igk_die(__METHOD__ . ' not implement');
        // $bck = $this->offset;
        // $out = $this->output; 
        // $this->output = '';
        // $this->offset = $from;
        // while(count($infos)>0){
        //     list($i, $s) = array_shift($infos);
        //     $this->replace($i,$s);
        // }
        // $this->output .= substr($this->source, $this->offset, $this->offset-$bck);
        // $v_out = $this->output;
        // // restaure backup
        // $this->offset = $bck;
        // $this->output = $out;
        // return $v_out;
    }
    /**
     * 
     * @param IRegexMatcherEndDetectionInfo $e 
     * @return void 
     * @throws Exception 
     */
    public function bindReplacement($e, ?callable $update = null)
    {
        $v_rp = &$this->replace;
        $buffer = $this;
        if ($e->parentInfo == null) {
            if ($v_rp) {
                $cp = array_pop($v_rp);
                if ($cp[0]->from == $e->from) {
                    self::BuildChain($this, $cp, $v_rp, $e, $update);
                   
                    $v_rp[] = $cp;
                } else {
                    igk_die('not a valid replacement list');
                } 
                while (count($v_rp) > 0) {
                    $q = array_shift($v_rp);
                    list($ee, $ss) = $q;
                    if (igk_getv($q,'rtrim')){
                        $buffer->rtrim();
                    }
                    if ($buffer->lineFeed) {
                        $buffer->output .= "\n" . $buffer->depth;
                    } else if ($buffer->isEmpty()) {
                        $buffer->output .= $buffer->depth;
                        $buffer->lineFeed = true;
                    } 
                    
                    $buffer->replace($ee, $ss);
                }
            }
        } else {
            if ($v_rp) {
                $cp = $v_rp[count($v_rp) - 1];
                if ($cp[0]->from == $e->from) {
                    $cp = array_pop($v_rp);
                    self::BuildChain($this, $cp, $v_rp, $e);
                    $v_rp[] = $cp;
                }
            }
        }
    }
    private static function BuildChain($t, &$cp, &$v_rp, $e, ?callable $update = null)
    {
        if ($chain = RegexMatcherUtility::GetChainUntil($v_rp, $e)) {
            $s =  RegexFormatMatcherUtility::ReplaceChain($e->value, $chain, $e->from, $t->initFormatBuilder());
             if ($update){
                $s = $update($s);
            }
            $cp[1] = '' . $s;
        }
    }
}
