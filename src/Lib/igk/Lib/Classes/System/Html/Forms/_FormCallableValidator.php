<?php
namespace IGK\System\Html\Forms;


class _FormCallableValidator implements IFormValidator{
    private $m_callable;
    public function __construct(callable $call)
    {
        $this->m_callable = $call;
    }
    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        $fc = $this->m_callable;       
        return $fc($value, $default, $fieldinfo, $error);
    }

}