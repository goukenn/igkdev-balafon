<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionEvalEngineTrait.php
// @date: 20240123 13:12:18
namespace IGK\System\Html\Templates\Engine\Traits;

use IGK\System\Html\HtmlReader;
use IGK\System\Templates\BindingExpressionReader;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Templates\Engine\Traits
* @author C.A.D. BONDJE DOUE
*/
trait ExpressionEvalEngineTrait{
    public static function EvalBindingExpression(string $content, array $data){
        // $data = (array)$options;
        $exp_reader = new BindingExpressionReader;
        $exp_reader->transformToEval = false;
        $exp_reader->skipMode = false;
        $exp_reader->expressionValueName = 'expression';
        $exp_reader->expressionArgs = [
            "expression" => "",
            HtmlReader::ARGS_ATTRIBUTE => HtmlReader::EXPRESSION_ARGS
        ];

        if ($c = preg_match_all('/(?P<escape>(\\\)?\')?\{\{(?P<value>.+)\}\}/', $content, $matches)){
            $tab = [];
            for($i = 0; $i < $c; $i++){
                $v_escape = $matches['escape'][$i];
                if ($v_escape=='\''){
                    continue;
                }
                $v_v = $matches[$i][0];
                if (!key_exists($v_v, $tab)){
                    $v_ts = substr($v_v, strlen($v_escape));

                   $v =  $exp_reader->treatContent($v_ts, $data);


                    //$v = igk_engine_eval($v_ts, 0, $data);
                    if ($v_escape=='\\\''){
                        $v_escape='\'';
                    }
                    $content = str_replace($v_v, $v_escape.$v, $content);
                    $tab[$v_v] = 1;
                }
            }
        } 
        return igk_str_remove_quote($content);
    }

}