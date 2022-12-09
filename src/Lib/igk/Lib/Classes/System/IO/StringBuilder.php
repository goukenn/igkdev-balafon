<?php
// @author: C.A.D. BONDJE DOUE
// @file: StringBuilder.php
// @desc: String builder helper
// @date: 20210723 13:22:40
namespace IGK\System\IO;


/**
 * string builder helper
 * @package IGK\System\IO
 */
class StringBuilder{
    protected $m_src;
    private $m_instop;
    
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
    
    public function __construct(& $src = null)
    {
        if ($src===null){
            $src  = "";
        } 
        $this->m_src = & $src;
    }
    /**
     * append text with line
     * @param string|array<string> $text 
     * @return void 
     */
    public function appendLine($text=""){
        $is_array = is_array($text);
        $tab = (is_string($text) ? [$text] : $text) ?? igk_die("not valid argument");
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
     * @return void 
     */
    public function append(string $text){     
        $this->m_src .= $text;       
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
}