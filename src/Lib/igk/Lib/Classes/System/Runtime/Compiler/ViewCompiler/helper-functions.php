<?php

use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewExpressionArgHelper;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewExpressionEval;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewExpressArg;
use IGK\System\ViewVarExpression;
use IGK\System\ViewEnvironmentArgs; 
use IGK\System\ViewExtractArgHelper;

if (!function_exists('igk_express_var')){
    function igk_express_var($name){
        $c = igk_environment()->peek(ViewEnvironmentArgs::class."/compiler_args");  
        if ( $c && isset($c->$name)){            
            if ($p = igk_getv($c, $name))
            return new ViewVarExpression($name, $p);           
        }
        if ($c){
            $var = $c->variables;
            $extract = igk_getv($var, "___IGK_PHP_EXTRACT_VAR___");
            if (key_exists($name, $var)){
                if ($extract){
                    return new ViewExtractArgHelper($name); 
                }
                return $var[$name];
            }
        }
        return "undefined";// '<?= $'.$name.' ? >';
    }
}

if (!function_exists('igk_php_expression')){
    function igk_php_expression($expression){
        return '<?= '.$expression.' ?>';
    }
}



if (!function_exists('igk_express_arg')){
    /**
     * used to fget view expression on arg node. will be detected as Iterable
     * @param mixed $expression 
     * @return mixed 
     * @throws IGKException 
     */
    function igk_express_arg($expression){   
        if ($expression==="this"){
            $expression = "ctrl";
        }
        $p = ViewExpressionArgHelper::GetVar($expression);
        if ($p instanceof HtmlNode){
            return $p;
        }
        if ($expression == ViewExpressionArgHelper::SETTER_VAR){
            return $p;
        }
        return new ViewExpressArg($expression, $p);
    }
}

if (!function_exists('igk_express_eval')){
    /**
     * express evaluate expression 
     * @param mixed $expression 
     * @return string 
     */
    function igk_express_eval($expression, array $dependOn=null){ 
        $g = new ViewExpressionEval($expression, $dependOn); 
        return $g; 
    }
}



if (!function_exists('igk_express_in_var')){
    /**
     * express evaluate expression 
     * @param mixed $expression 
     * @return string 
     */
    function igk_express_in_var($expression){  
        return ''.$expression;
    }
}
if (!function_exists('igk_express_litteral_var')){
    /**
     * express evaluate expression 
     * @param mixed $expression 
     * @return string 
     */
    function igk_express_litteral_var(string $name){  
        return '<?= $'.$name.' /* litteral var */ ?>';
    }
}


if (!function_exists('igk_eval_expression')){
    /**
     * express evaluate expression 
     * @param mixed $expression 
     * @return string 
     */
    function igk_eval_expression(string $name){  
        igk_wln( __FILE__.":".__LINE__, "express", $name);
        return 180; ///'<?= $'.$name.' /* eval expression */ ? >';
    }
}

