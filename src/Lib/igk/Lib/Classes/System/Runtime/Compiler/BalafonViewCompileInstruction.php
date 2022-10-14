<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonViewCompileInstruction.php
// @date: 20221012 11:06:33
namespace IGK\System\Runtime\Compiler;

use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\IO\StringBuilder;
use IGK\System\Runtime\Compiler\Html\CompilerNodeModifyDetector;
use IGK\System\ViewEnvironmentArgs;
use IGKException;

require_once __DIR__."/helper-functions.php";
///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class BalafonViewCompileInstruction{
    var $data;
    var $variables = [];
    var $output;
    var $controller;
    /**
     * extract variable
     * @var false
     */
    var $extract = false;
 
    /**
     * compile instruction
     * @return bool|string 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function compile(){  
        if (!is_array($this->data)){
            return false;
        }
        $_eval = \Closure::fromCallable(function(){
            foreach(array_keys(func_get_arg(1)->data) as $_){
                if ($_=="_"){
                    igk_die("_ is reserved variable");
                }
               $$_ = & func_get_arg(1)->data[$_] ;
            }
            unset($_);
            // igk_debug_wln("eval:".func_get_arg(0));
            // igk_ilog("eval : ".func_get_arg(0) );
            ob_start();
            eval(func_get_arg(0));
            $buffer = trim(ob_get_contents());
            // igk_debug_wln_e("first:".$buffer, "x value:", $x);
            ob_end_clean();
            if (!empty($buffer) || $t->getModify()){
                if ($t->getModify()){
                    $t->text($buffer);
                    return;
                } 
                return func_get_arg(0);                
            }
            return func_get_arg(0);
        })->bindTo($this->controller);
        $out_put = "";
        $sb = new StringBuilder($out_put);
        $detector = new CompilerNodeModifyDetector;
        $init = CompilerNodeModifyDetector::Init();
        $vars = & $this->variables ;
        $bck = igk_getv($vars, "t", igk_create_node('notagnode'));
        $vars["t"] = $detector; 
        $vars["___IGK_PHP_EXPRESS_VAR___"] = "igk_express_ecapsed_string";
        $vars["___IGK_PHP_EXPRESS_VAR___"] = "igk_express_var";
        $vars["___IGK_PHP_VAR___"] = "igk_express_litteral_var";

        $vars["___IGK_PHP_EXTRACT_VAR___"] = $this->extract;

        // igk_wln_e("extract .....", $this->extract);
       
        $vars_clone = array_merge($vars);
        igk_environment()->push(ViewEnvironmentArgs::class."/compiler_args", (object)[
            "variables"=>& $vars
        ]);
        $pass = (object)["data"=>& $vars];
        foreach($this->data as $k){
            if (!empty($k->value)){
                // igk_debug_wln("to eval : ", $k->value);   
                if ($n = $_eval($k->value, $pass)){
                    $sb->appendLine($n);
                }
            }
        } 
        // igk_wln_e("done:compile instruction", $sb."", $vars["t"]->render());
        igk_environment()->pop(ViewEnvironmentArgs::class."/compiler_args");
        if ($init){
            CompilerNodeModifyDetector::UnInit();
        }
        if ($vars_clone === $vars){
            // if array are equals
        }
        if ($detector->getModify() && $bck){
            $bck->add($detector);
            $sb->appendLine("?>".trim($bck->render())."<?php");
            $detector->clearChilds();
        }
        return $out_put;
    }
    
}