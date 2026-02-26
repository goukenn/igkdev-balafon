<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexDetectBuffer.php
// @date: 20250702 12:29:43
namespace IGK\System\Text;

use Exception;
use IGK\System\Console\Logger;

/**
 * 
 * @package IGK\System\Text
 * @author C.A.D. BONDJE DOUE
 */
class RegexDetectBuffer
{
    /**
     * direct output 
     * @var string
     */
    var $output = '';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $offset = 0;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $source;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $lineFeedSeparator = "\n";
    /**
     * 
     * @var ?string
     */
    var $depth;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $lineFeed = false;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $replace = [];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $tabListener;
    /**
     * @var ?string
     */
    var $flag;

    /**
    * auto generate doc.
    * @var mixed
    */
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
     * @return string 
     */

    public function __toString()
    {
        return $this->output.'';
    }
    /**
     * 
     * @return bool 
     */

    public function isEmpty()
    {
        return empty(trim($this->output));
    }
    /**
     * replace or append value to buffer
     * @param RegexMatcherCapture $e 
     * @param string $value 
     * @return void 
     */

    function replace(RegexMatcherCapture $e, string $value = '')
    {
        igk_is_debug() && Logger::warn('replace: ['.json_encode($value).']');
        $v_before = substr($this->source, $this->offset, $e->from - $this->offset);
        if ($this->flag == 'single-comment'){
            if ($v_before=="\n"){
                $v_before='';
            }
            $this->flag = null;
        }
        if ($this->lineFeed) {
            $v_before = ltrim($v_before);
            $this->lineFeed = false;
        }
        $this->output .= $v_before . $value;
        $this->offset = $e->to;
        $this->lineFeed = $this->lineFeed || $this->checkLineFeed($value);
    }

    /**
    * auto generate doc.
    * @param string $v
    */
    public function checkLineFeed(string $v)
    {
        return preg_match("/\}$/", $v);
    }

    /**
    * auto generate doc.
    */
    function end()
    {
        $this->output .= substr($this->source, $this->offset);
    }

    /**
    * auto generate doc.
    */
    function output()
    {
        $this->end();
        return $this->output;
    }

    /**
    * auto generate doc.
    */
    function rtrim(){
        $this->output = rtrim($this->output);
    }

    /**
    * auto generate doc.
    */
    public function initFormatBuilder(){
        if ($fc = $this->m_initFormatBuilderListener){
            return $fc() ?? igk_die('failed to initialize listener');
        }
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
                        $buffer->output .= $this->lineFeedSeparator . $buffer->depth;
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

    /**
    * auto generate doc.
    * @return int
    */
    public function outputLength():int{
        return strlen($this->output);
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

    /**
    * auto generate doc.
    */
    public function clearOutput(){
        $this->output = '';
    }
    /**
     * append to output 
     * @param string $source 
     * @return void 
     */

    public function append(string $source){
        $this->output.= $source;
    }
    /**
     * generate tab 
     * @param null|int $depth 
     * @return string 
     */

    public function tab(?int $depth=null):string{
        if ($fc = $this->tabListener){
            return $fc($depth);
        }
        return $this->depth;
    }
}
