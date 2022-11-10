<?php

// @author: C.A.D. BONDJE DOUE
// @filename: PhpInterfaceDocument.php
// @date: 20220601 14:25:25
// @desc: php interface document 

namespace IGK\System\IO\File\Php;

use IGK\System\IO\File\PHPScriptBuilder;

class PhpInterfaceDocument{
    public $type = "interface";
    public $name;
    public $namespace;
    public $file;
    public $doc;
    private $m_listener;

    /**
     * 
     * @param ?callable|IInvokeAction #phpDocListener
     * @return void 
     */
    public function __construct($phpDocListener)
    {
        $this->m_listener = $phpDocListener;
    }

    public function generate(){
        $o = $this->_getPhpDoc();
        $builder = new PHPScriptBuilder();        
        $builder->type($this->type)
            ->namespace($this->namespace)
            ->name($this->name)
            ->file($this->file)
            ->doc($this->doc)
            ->defs($this->defs ?? "// extract.") 
            ->phpdoc($o);
        return $builder->render();
    }
    private function _getPhpDoc(){
        if ($this->m_listener){
            if (is_callable($this->m_listener))
                return call_user_func_array($this->m_listener, []);
            else 
                return $this->m_listener->invoke();
        }
    }
}

