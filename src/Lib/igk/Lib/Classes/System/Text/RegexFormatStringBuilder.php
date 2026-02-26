<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexFormatStringBuilder.php
// @date: 20250712 12:53:39
namespace IGK\System\Text;

use IGK\System\IO\StringBuilder;

/**
 * base regex format string builder
 * @package IGK\System\Text
 * @author C.A.D. BONDJE DOUE
 */
class RegexFormatStringBuilder
{
    /**
     * 
     * @var StringBuilder
     */
    protected $m_sb;
    /**
     * line feed flag
     * @var bool
     */
    var $lineFeed;

    /**
    * Property: tab stop.
    * @var mixed
    */
    var $tabStop = '    ';

    /**
    * Property: depth.
    * @var mixed
    */
    var $depth = 0;

    /**
    * Property: inline comment prefix.
    * @var mixed
    */
    var $inlineCommentPrefix = "\r\t\t";

    /**
    * Property: space.
    * @var mixed
    */
    var $space = ' ';
    /**
     * 
     * @var mixed
     */
    var $noInlinePrefixComment;
    /**
     * get tab display
     * @return string 
     */

    public function tab(?int $depth=null):string
    {
        return str_repeat($this->tabStop, $depth ?? $this->depth);
    }
    /**
     * append line definition 
     * @param string $data 
     * @return void 
     */

    public function append(string $data)
    {
        if ($this->lineFeed) {
            $s = ltrim($data);
            if (empty($s)) {
                return;
            }
            $this->m_sb->rtrim();
            if (!$this->m_sb->isEmpty()) {
                $this->m_sb->append("\n");
            }
            $data = $this->tab() . $s;
        }
        $this->lineFeed = false;
        $this->m_sb->append($data);
    }

    /**
    * .ctr
    */
    public function __construct()
    {
        $this->m_sb = new StringBuilder;
    }
    /**
     * rtrim 
     * @return StringBuilder 
     */

    public function rtrim()
    {
        return $this->m_sb->rtrim();
    }

    /**
    * get string presentation.
    */
    public function __toString()
    {
        return $this->m_sb . '';
    }
    /**
     * clue definition 
     * @param string $c 
     * @param string $depth 
     * @return string 
     */

    public static function ClueDef(string $c, string $depth)
    {
        $tab = explode("\n", $c);
        $lines = [];
        $first = true;
        $mark = false;
        $contents = false;
        foreach ($tab as $l) {
            if (empty(trim($l))) {
                if (!$mark) {
                    $lines[] = '';
                    $mark = true;
                }
                $first = false;
                continue;
            }
            if ($mark && $contents) {
                array_pop($lines);
            }
            if ($first) {
                $lines[] = $l;
            } else {
                $lines[] = $depth . ltrim($l);
            }
            $mark = false;
            $first = false;
            $contents = true;
        }
        if (!$contents && $mark) {
            return "\n";
        }
        return implode("\n", $lines);
    }
    /**
     * append inline prefix depth
     * @return static
     */

    public function appendPrefixInlineComment(){
        if ($this->noInlinePrefixComment){
            $this->appendSpace();
            return $this;
        }
        $c = $this->inlineCommentPrefix;
        $this->append($c.$this->tab(1));
        return $this; 
    }

    /**
    * Append space.
    */
    protected function appendSpace(){
        $this->append($this->space);
        return $this;
    }

    /**
    * Outputs Length.
    * @return int
    */
    public function outputLength():int{
        return $this->m_sb->length();
    }
}
