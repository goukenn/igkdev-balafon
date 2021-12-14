<?php
// @author: C.A.D. BONDJE DOUE
// @file: StringLogBuilder.php
// @desc: 
// @date: 20210723 13:13:48
namespace IGK\System\IO;


class StringLogBuilder extends StringBuilder{
    private $m_listener;

    public function __construct(& $src, callable $listener)
    {
        parent::__construct($src);        
        $this->m_listener = $listener;
    }
    public function appendLine($text){
        $this->append($text."\n");
    }
    public function append($text){
        if (($fc = $this->m_listener)&& $fc()){            
            $this->m_src .= $text; 
        }
    }
}