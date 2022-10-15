<?php

use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Runtime\Compiler\ViewExpressionArgHelper;
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
    function igk_express_arg($expression){         
        $p = ViewExpressionArgHelper::GetVar($expression);
        if ($p instanceof HtmlNode){
            return $p;
        }
        if ($expression == ViewExpressionArgHelper::SETTER_VAR){
            return $p;
        }
        igk_wln_e("the expression", $expression, ViewExpressionArgHelper::SETTER_VAR);
        return '<?= $'.$expression.' /* igk_express_arg */ ?>';
    }
}

if (!function_exists('igk_express_eval')){
    /**
     * express evaluate expression 
     * @param mixed $expression 
     * @return string 
     */
    function igk_express_eval($expression){ 
    
        $c = igk_environment()->peek(ViewEnvironmentArgs::class."/compiler_args");   
        $var = $c->variables;
        $extract = igk_getv($var, "___IGK_PHP_EXTRACT_VAR___");
        if ($extract){
            extract ($var);
            return eval('return '.$expression.';');
        } 
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

