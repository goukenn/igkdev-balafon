<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexDetectBuffer.php
// @date: 20250702 12:29:43
namespace IGK\System\Text;


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
    function replace($e, $value = '')
    {
        $this->output .= substr($this->source, $this->offset, $e->from - $this->offset) . $value;
        $this->offset = $e->to;
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
}
