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
    private $m_sb;
    /**
     * line feed flag
     * @var bool
     */
    var $lineFeed;
    var $tabStop = '    ';
    var $depth = 0;
    public function tab()
    {
        return str_repeat($this->tabStop, $this->depth);
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
    public function __construct()
    {
        $this->m_sb = new StringBuilder;
    }
    public function rtrim()
    {
        return $this->m_sb->rtrim();
    }
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
}
