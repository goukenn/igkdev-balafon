<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionEvalEngineTrait.php
// @date: 20240123 13:12:18
namespace IGK\System\Html\Templates\Engine\Traits;
use Exception;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\HtmlReader;
use IGK\System\Templates\BindingExpressionReader;
use IGK\System\Text\RegexMatcherContainer;
use IGKException;
use ReflectionException;

/**
* auto generate doc.
* @package IGK\System\Html\Templates\Engine\Traits
* @author C.A.D. BONDJE DOUE
*/
trait ExpressionEvalEngineTrait
{
    /**
     * eval mustache expression and [[:@raw]] | [[:@ctl]] expression - for other just use global definition 
     * @param string $content 
     * @param array $data 
     * @return string 
     * @throws Exception 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function EvalBindingExpression(string $content, array $data)
    {
        $exp_reader = new BindingExpressionReader;
        $exp_reader->transformToEval = false;
        $exp_reader->skipMode = false;
        $exp_reader->expressionValueName = 'expression';
        $exp_reader->expressionArgs = [
            "expression" => "",
            HtmlReader::ARGS_ATTRIBUTE => HtmlReader::EXPRESSION_ARGS
        ];
        $rgx = new RegexMatcherContainer;
        $rgx->begin('(?P<escape>(?:\\\)?\')?\{\{', '\}\}', 'capture');
        $offset = 0;
        $pos = 0;
        $s = '';
        while ($g = $rgx->detect($content, $pos)) {
            if ($e = $rgx->end($g, $content, $pos)) {
                if (is_null($e->parentInfo)) {
                    $v_escape = isset($e->beginCaptures['escape']) ? $e->beginCaptures['escape'][0] : '';
                    if ($v_escape == '\'') {
                        continue;
                    }
                    $v = $exp_reader->treatContent($e->value, $data);
                    $v = igk_str_remove_quote($v);
                    if ($v_escape == '\\\'') {
                        $v_escape = '\'';
                    }
                    $s .= substr($content, $offset, $e->from-$offset) . $v_escape . $v;
                    $offset = $e->to;
                } 
            }
        }
        $s .= substr($content, $offset);
        return $s; 
    }
}