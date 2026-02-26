<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewHandler.php
// @date: 20221031 15:43:24
namespace IGK\System\Runtime\Compiler\ViewCompiler;
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ViewHandler{

    /**
    * Property: instance.
    * @var mixed
    */
    private static $sm_instance;

    /**
    * Property: tab.
    * @var mixed
    */
    var $tab = [];

    /**
    * Property: attrib bind.
    * @var mixed
    */
    var $attribBind = false;

    /**
    * Returns Instance.
    */
    public static function getInstance(){
        is_null(self::$sm_instance) && self::$sm_instance = new self;
        return self::$sm_instance;
    }
    private function __construct(){  
        $this->tab = ["class"=>null, "style"=>null];      
    }

    /**
    * Attrib string.
    */
    function attribString(){
        $s = ""; 
        foreach ($this->tab as $key => $value) {
            if (is_null($value))
                continue;            
            $s.= " ".$key.'="'.trim($value,'"').'"';
        }
        return $s;
    }
}