<?php
// @author: C.A.D. BONDJE DOUE
// @file: StringLogBuilder.php
// @desc: 
// @date: 20210723 13:13:48
namespace IGK\System\IO;

/**
* auto generate doc.
* @package IGK\System\IO
*/
class StringLogBuilder extends StringBuilder{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_listener;

    /**
    * .ctr
    * @param mixed & $src
    * @param callable $listener
    */
    public function __construct(& $src, callable $listener)
    {
        parent::__construct($src);        
        $this->m_listener = $listener;
    }

    /**
    * auto generate doc.
    * @param mixed $text
    */

    public function appendLine($text){
        $this->append($text."\n");
    }

    /**
    * auto generate doc.
    * @param mixed $text
    */

    public function append($text){
        if (($fc = $this->m_listener)&& $fc()){            
            $this->m_src .= $text; 
        }
    }
}