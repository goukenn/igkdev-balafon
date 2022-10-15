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
     * code header 
     * @var string code
     */
    var $header;
    /**
     * extract variable
     * @var false
     */
    var $extract = false;
    
    private static $sm_SOURCE;
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
            if (func_num_args()>2){
                self::$sm_SOURCE = implode("\n", [func_get_arg(2), func_get_arg(0)]);
                eval("?>".self::$sm_SOURCE); 
                // implode("\n", [func_get_arg(2), func_get_arg(0)]));
                //igk_wln_e(__FILE__.":".__LINE__,  "using .....", self::$sm_SOURCE);
            }else {
                eval(func_get_arg(0));
            }
            $buffer = trim(ob_get_contents());
            // igk_debug_wln_e("first:".$buffer, "x value:", $x);
            ob_end_clean();
            if (!empty($buffer) || $t->getModify()){
                if ($t->getModify()){
                    // $t->text($buffer);
                    return;
                } 
                return func_get_arg(0);                
            }
            if ($t->getParam(CompilerNodeModifyDetector::CLEAR_FLAG_PARAM)){
                $t->setParam(CompilerNodeModifyDetector::CLEAR_FLAG_PARAM, null);
                return null;
            }
            if ($___IGK_PHP_SETTER___->getIsUpdate()){
                $___IGK_PHP_SETTER___->resetUpdate();
                return null;
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
        // $vars["___IGK_PHP_EXPRESS_VAR___"] = "igk_express_ecapsed_string";
        $vars["___IGK_PHP_EXPRESS_VAR___"] = "igk_express_var";
        // $vars["___IGK_PHP_VAR___"] = "igk_express_litteral_var";
        $vars["___IGK_PHP_EXTRACT_VAR___"] = $this->extract;
        $vars["___IGK_PHP_SETTER___"] = new ViewExpressionSetter($vars);

        // igk_wln_e("extract .....", $this->extract);
       
        $vars_clone = array_merge($vars);
        igk_environment()->push(ViewEnvironmentArgs::class."/compiler_args", (object)[
            "variables"=>& $vars
        ]);
        $pass = (object)["data"=>& $vars];
        $qtab = $this->data;
        $nodes = [];
        $p = null;
        while(count($qtab)>0){
            $k = array_shift($qtab);
            // $sb->appendLine("// detect:");
            if ($k instanceof ReadBlockInstructionInfo){ 
                igk_debug_wln(
                    __FILE__.":".__LINE__, 
                    "Instruction info read ");
                if ((count($nodes)>0) && ($nodes[0]=== $k)){
                    igk_debug_wln(__FILE__.":".__LINE__,  "finish node:");
                    array_shift($nodes);
                    $p = count($nodes)>0 ? $nodes[0] : null;
                    $n = trim($k->compile());
                    // remove php block code...
                    if (strpos($n, "<?php")===0){
                        $n = ltrim(substr($n, 5));
                    }
                    if (($pos = strrpos($n, "?>"))!==false){
                        $n = substr($n, 0, $pos);
                    }
                    if ($p === null){
                        //render to root
                        $sb->appendLine($n);
                    } else {
                        $p->compile_result .= $n."// in";
                    }
                    continue;
                }
                array_unshift($qtab, ...array_merge($k->codeBlocks? $k->codeBlocks: [],
                [$k]));
                array_unshift($nodes, $k);
                $p = $k;
                $detector->clearChilds();
                continue;
            }  
            $n = null;
            $t = $detector;
            if (!empty($k->value)){
                $detector->setFreezeClearModify(true);
                $n = $_eval($k->value, $pass, $this->header);
                $detector->setFreezeClearModify(false);
                // in subchilds
                if ($t->getModify()){
                    $n = $n."?>".$vars["t"]->render()."<?php ";
                    $t->clearChilds();
                }
            }

            if ($p){
                $p->compile_result .= $n;
                igk_wln(__FILE__.":".__LINE__,  "sublock",  $n);
            } else { 
                if (!empty($n))                           
                $sb->appendLine($n);                
            }
        }


        // foreach($this->data as $k){
        //     if ($k instanceof ReadBlockInstructionInfo){
                
        //         igk_wln_e(__FILE__.":".__LINE__,  $k->codeBlocks[0]);
        //     }
        //     else if (!empty($k->value)){
        //         // igk_debug_wln_e("to eval : ", $k->value, $this->header."");   
        //         if ($n = $_eval($k->value, $pass, $this->header)){
        //             $sb->appendLine($n);
        //         }
        //     }
        // } 
        igk_wln_e(
            __FILE__.":".__LINE__, 
            "done:compile instruction", $sb."");

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