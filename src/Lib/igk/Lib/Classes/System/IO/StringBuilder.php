<?php
// @author: C.A.D. BONDJE DOUE
// @file: StringBuilder.php
// @desc: String builder helper
// @date: 20210723 13:22:40
namespace IGK\System\IO;
use igk;
/**
 * string builder helper
 * @package IGK\System\IO
 */
class StringBuilder{
    /**
    * Property: src.
    * @var mixed
    */
    protected $m_src;
    /**
    * Property: instop.
    * @var mixed
    */
    private $m_instop;
    /**
    * Constant: trim charlist.
    * @var mixed
    */
    const TRIM_CHARLIST = " \t\n\r\0\x0B";
    /**
     * line feed symbol
     * @var string
     */
    var $lf = "\n";
    /**
     * tab stop after line 
     * @var string
     */
    var $tabstop='';
    /**
    * .ctr
    * @param null|string & $src
    */
    public function __construct(?string & $src = null)
    {
        if ($src===null){
            $src  = "";
        } 
        $this->m_src = & $src;
    }
    /**
    * auto generate doc.
    * @param null|int $length
    * @return $this
    */
    public function replaceWithFrom(string $text, int $offset, ?int $length=null){
        $g = igk_str_rm($this->m_src, $offset, $length);
        $g = igk_str_insert($text, $g, $offset);
        $this->m_src = $g;
        return $this;
    }
    /**
    * auto generate doc.
    * @param int $offset
    * @return $this
    */
    public function insertAt(string $text, int $offset){
        $this->m_src = igk_str_insert($text, $this->m_src, $offset);
        return $this;
    }
    /**
     * append text with line
     * @param string|array<string> $text 
     * @return void 
     */
    public function appendLine($text=""){
        //+ | BUG FIX infine loop
        $is_array = is_array($text);
        $tab = (is_string($text) || !is_array($text) ? [$text] : $text) ?? igk_die("not valid argument");
        if (count($tab)){
            while(count($tab)>0){
                $text = array_shift($tab);
                if ($this->m_instop){
                    $this->append($this->tabstop);
                }
                if ($is_array){
                    $text = trim($text);
                }
                $this->append($text.$this->lf);
                $this->m_instop = 1;
            }
        }
    }
    /**
     * prepend text
     */
    public function prependLine($text){
        $cp = $this->m_src;
        $this->m_src = "";
        $this->appendLine($text);
        $this->m_src .= $cp;        
    }
    /**
     * append text
     * @param string $text 
     * @return static 
     */
    public function append(string $text): StringBuilder{      
        $this->m_src .= $text;   
        return $this;    
    }
    /**
    * Rm last.
    * @param string $text
    * @param mixed $number
    * @return StringBuilder
    */
    public function rmLast(string $text, $number=1): StringBuilder{
        $this->m_src = igk_str_rm_last($this->m_src, $text, $number);
        return $this;
    }
    /**
     * rtrim
     * @param string $charlist 
     * @return $this 
     */
    public function rtrim(string $charlist=self::TRIM_CHARLIST){
        $this->m_src = rtrim($this->m_src, $charlist);
        return $this;
    }
    /**
     * ltrim
     * @param string $charlist 
     * @return $this 
     */
    public function ltrim(string $charlist=self::TRIM_CHARLIST){
        $this->m_src = ltrim($this->m_src, $charlist);
        return $this;
    }
    /**
     * trim data
     * @param string $charlist 
     * @return $this 
     */
    public function trim(string $charlist= self::TRIM_CHARLIST){
        $this->m_src = trim($this->m_src, $charlist);
        return $this;
    }
    /**
     * return the string builder
     * @return mixed 
     */
    public function __toString(){
        return $this->m_src;
    }
    /**
     * clear current buffer
     * @return void 
     */
    public function clear(){
        $this->m_src = "";
    }
    /**
     * get if buffer is empty
     * @return bool 
     */
    public function isEmpty(){
        return empty($this->m_src);
    }
    /**
     * replace string
     * @param string $hastack 
     * @param string $with 
     * @return $this 
     */
    public function replace(string $hastack, string $with){
        $this->m_src = str_replace($hastack, $with, $this->m_src);
        return $this;
    }
    /**
    * Length.
    * @return int
    */
    public function length(): int{
        return strlen($this->m_src);
    }
    /**
     * set new string value
     * @param string $new 
     * @return $this 
     */
    public function set(string $new){
        $this->m_src = $new;
        return $this;
    }
}