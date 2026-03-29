<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhpRemoveGlobaFunc.php
// @date: 20260320 14:22:53
namespace IGK\System\Php\Helper;

use IGK\Helper\StringUtility;
use IGK\System\Console\Logger;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;

/**
* auto generate doc.
* @package IGK\System\Php\Helper
* @author C.A.D. BONDJE DOUE
*/

/**
* auto generate doc.
* @package IGK\System\Php\Helper
*/
class PhpRemoveGlobaFunc
{
    var $removeEmptyLine;

    /**
    * auto generate doc.
    * @return RegexMatcherContainer
    */
    protected function _regexDefinition()
    {
        $p = new RegexMatcherContainer;
        $p->begin('\\b(use)\\b', '(?<=;)')->last();
        $p->begin('\?>', '<\?(php\\b|=)', 'outside-php-block')->last();
        $v_rp_php_doc = $p->begin('\/\*\*', '\*\/', 'php-doc')->last();
        $v_rp_empty_line = $p->match('^(\\s*?\\n)+', 'rp-empty-line')->last();

        $v_rp_cond = $p->begin('\\b(if|else|else)\\b', '(?<=;|\})')->last();
        $v_rp_func = $p->begin('\\b(function)\\b', '(?<=(;|\}))', 'rp-function')->last();
        $v_rp_block = $p->begin('\\{', '\\}', 'rp-block')->last();
        $comment = [];
        $comment[] = $p->appendMultilineComment()->last();
        $comment[] = $p->appendSingleLineComment()->last();
        $heredoc = [];
        $string = $p->appendStringDetection('string', true)->last();
        RegexMatcherUtility::AppendPhpHereDoc($p, $heredoc);

        $v_rp_cond_block = $p->begin('\{', '\}', 'rp-cond-block')->last();

        $v_rp_cond->patterns = [
            $v_rp_php_doc,
            $v_rp_cond_block,
            $v_rp_empty_line
        ];

        $v_off = [
            $comment,
            $heredoc,
            $string,
        ];
        $v_parenthese = $p->createPattern([
            'tokenID' => 'rp-parenthese',
            'begin' => '\\(',
            'end' => '\\)'
        ]);

        $v_parenthese->patterns = [
            $v_off,
            $v_parenthese
        ];
        $v_rp_param = $p->createPattern([
            'tokenID' => 'rp-param',
            'begin' => '\(',
            'end' => '\)'
        ]);
        $v_rp_name = $p->createPattern([
            'tokenID' => 'rp-name',
            'match' => '\\b([a-zA-Z_][a-zA-Z_0-9]*)\\b'
        ]);
        $v_rp_param->patterns = [
            $comment,
            $heredoc,
            $string,
            $v_parenthese
        ];


        $v_rp_block->patterns = [
            $comment,
            $heredoc,
            $string,
            $v_rp_block
        ];
        $v_rp_func->patterns = [
            $comment,
            $heredoc,
            $string,
            $v_rp_block,
            $v_rp_param,
            $v_rp_name,
        ];
        $v_rp_cond_block->patterns = [
            $v_rp_empty_line,
            $v_rp_php_doc,
            $comment,
            $heredoc,
            $v_rp_func,
        ];
        return $p;
    }

    /**
    * auto generate doc.
    * @return array{function: \Closure(mixed $e, mixed $fc_info): void}
    */
    protected function _getFuncHandle()
    {
        return [
            'rp-param' => function ($e, $fc_info) {                
                $fc_info->param = $e->value;
            },
            'php-doc' => function ($e, $fc_info) {
                if (preg_match(
                    '/\\bfunction\\b/',
                    $fc_info->src,
                    $tab,
                    PREG_OFFSET_CAPTURE,
                    $e->to
                )) {
                    $ct = substr($fc_info->src, $e->to, $tab[0][1] - $e->to);
                    if (empty(trim($ct))) {
                        // remove php doc
                        $fc_info->replacements[] = [$e->from, $e->to];
                    }
                } else {
                    if (empty(trim(substr($fc_info->src, $e->to, strlen($fc_info->src) - $e->to)))) {
                        $fc_info->replacements[] = [$e->from, $e->to];
                    }
                }
            },
            'rp-empty-line' => function ($e, $fc_info) {
                if ($this->removeEmptyLine) {
                    $fc_info->replacements[] = [$e->from, $e->to];
                }
            },
            'rp-function' => function ($e, $fc_info) {
                if ($fc_info->name) {
                    $fc_info->replacements[] = [$e->from, $e->to];
                }
                $fc_info->name = null;
                $fc_info->param = false;
            },
            'rp-name' => function ($e, $fc_info) {
                if (!$fc_info->param ){
                    $fc_info->name = $e->value;
                }
            }
        ];
    }

    /**
    * auto generate doc.
    * @param string $src
    * @return void
    */
    public function remove(string $src)
    {
        $regex = $this->_regexDefinition();
        $pos = 0;
        $fc_info = (object)[
            'name' => null,
            'ignoreName' => false, // <- for anonym declaration function()use(){}
            'param' => false,
            'src' => $src,
            'replacements' => []
        ];
        // define
        $o_crc = 0;
        $fc_handle = $this->_getFuncHandle();
        $is_debug = igk_is_debug();
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                $id = $e->tokenID;
                $is_debug && Logger::info(sprintf('rp-func: %-15s - [%s]', $id, json_encode($e->value)));
                if ($id && ($fc = igk_getv($fc_handle, $id))) {
                    $fc($e, $fc_info);
                }
            }
        }
        $g = implode("\n", array_map('trim',  StringUtility::SplitLitteral($src, $fc_info->replacements)));
        return $g;
    }
}
