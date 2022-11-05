<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewHandler.php
// @date: 20221031 15:43:24
namespace IGK\System\Runtime\Compiler\ViewCompiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ViewHandler{
    private static $sm_instance;
    var $tab = [];
    var $attrbInd = false;
    public static function getInstance(){
        is_null(self::$sm_instance) && self::$sm_instance = new self;
        return self::$sm_instance;
    }
    private function __construct(){  
        $this->tab = ["class"=>null, "style"=>null];      
    }
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