<?php
// @author: C.A.D. BONDJE DOUE
// @file: StringBuilder.php
// @desc: String builder helper
// @date: 20210723 13:22:40
namespace IGK\System\IO;


class StringBuilder{
    protected $m_src;
    
    public function __construct(& $src = null)
    {
        if ($src===null){
            $src  = "";
        }
        $this->m_src = & $src;
    }
    public function appendLine($text){
        $this->append($text."\n");
    }
    public function append($text){          
        $this->m_src .= $text;       
    }
    /**
     * return the string builder
     * @return mixed 
     */
    public function __toString(){
        return $this->m_src;
    }
    public function clear(){
        $this->m_src = "";
    }
}