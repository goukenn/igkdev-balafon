<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerTokenBracketTrait.php
// @date: 20221021 11:10:03
namespace IGK\System\Runtime\Compiler\Traits;

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler\Traits
*/
trait CompilerTokenBracketTrait{

    /**
    * Check bracket.
    * @param mixed $options
    * @param mixed $value
    */
    protected function _checkBracket($options, $value)
    {
        // update branket value
        switch ($value) {
            case '{':
            case '(':
            case '[':
                $options->depth++;
                break;
            case '}':
            case ')':
            case ']':
                $options->depth--;
                break;
        }
        switch($value){
            case "}":
                $options->bracketDepth = max(0, $options->bracketDepth- 1);
                break;
            case "{":
                $options->bracketDepth++;
                break;
        }
    }
}