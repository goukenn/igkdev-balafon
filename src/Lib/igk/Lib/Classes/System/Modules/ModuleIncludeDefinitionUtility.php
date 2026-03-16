<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleIncludeDefinitionUtility
// @date: 20260228 13:59:29
namespace IGK\System\Modules;

use IGK\Helper\StringUtility;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilderUtility;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;

/**
* auto generate doc.
* @package IGK
* @author C.A.D. BONDJE DOUE
*/

/**
* auto generate doc.
* @package IGK\System\Modules
*/
class ModuleIncludeDefinitionUtility
{
    const DEBUG_KEY = 'debug-module-include-utility';

    /**
    * auto generate doc.
    * @param mixed $params
    * @param mixed $args
    * @param null|mixed $src_obj
    * @return
    */
    public static function Treat($params, $args, $src_obj = null)
    {
        $default = [];
        $g = PHPScriptBuilderUtility::ExtractArgsFromFuncParamDefinition($params, $default, $src_obj);
        $t = [];
        while ((count($args) > 0) && (count($g) > 0)) {
            $q = array_shift($g);
            $t[$q] = array_shift($args);
        }
        while (count($g) > 0) {
            $q = array_shift($g);
            $t[$q] = igk_getv($default, $q);
        }
        return $t;
    }

    /**
    * auto generate doc.
    * @param mixed $param
    * @param mixed $code
    * @param string $selfKey
    * @return
    */
    public static function CreateMethodHandle($param, $code, string $selfKey = '_this')
    {
        $fc = (function ($param, $code, $selfKey) {
            $__def = [
                $param,
                $code,
                'invoke' => function () {
                    extract(func_get_arg(0));
                    return eval(func_get_arg(1));
                }
            ];
            return function () use ($__def, $selfKey) {
                $tab = self::Treat($__def[0], func_get_args(), $this);
                $tab[$selfKey] = $this;
                return call_user_func_array($__def['invoke']->bindTo($this), [$tab, $__def[1]]);
            };
        })($param, $code, $selfKey);

        return $fc;
    }

    /**
    * auto generate doc.
    * @param string $file
    * @param array &$reference
    * @param null|array $fc_handle
    * @return array info to caches
    */
    public static function BindFile(string $file, ?array &$reference, ?array $fc_handle = []): array
    {
        $src = file_get_contents($file);
        return self::BindSourceFile($src, $file, $reference, $fc_handle);
    }
    /**
     * bind source file to get defined global functions
     * @param string $source 
     * @param string $file 
     * @param null|array &$reference 
     * @param null|array $fc_handle 
     * @return array 
     */
    public static function BindSourceFile(string $source, string $file, ?array &$reference, ?array $fc_handle = [])
    {

        $src = $source;
        $regex = self::InitRegexContainer();
        $caches = [];
        $pos = 0;
        $y = 0;
        $fc_info = (object)[
            'file' => $file,
            'line' => 0,
            'name' => null,
            'params' => null,
            'code' => null
        ];
        $fc_handle = array_merge([
            'func-name' => function ($e) use ($fc_info) {
                if (is_null($fc_info->name) && !is_null($fc_info->params)){
                      $fc_info->name = null;
                      $fc_info->params = null;
                      return;
                }
                if ($fc_info->name){
                    if ('use' == trim($e->value)){
                    $fc_info->name = null;
                    }
                    return;
                }
                $fc_info->name = igk_conf_get($e->beginCaptures, 'n/0');
            },
            'func-param' => function ($e) use ($fc_info) {
                $param = substr($e->value, 1, -1);
                $fc_info->params = $param;
            },
            'func-code-block' => function ($e) use ($fc_info) {
                $v = substr($e->value, 1, -1);
                $fc_info->code = $v;
            },
            'function' => function ($e) use ($fc_info, &$reference, &$caches) {
                if ($fc_info->name && $fc_info->code) {
                    $g = self::CreateMethodHandle($fc_info->params, $fc_info->code);
                    $caches[$fc_info->name] = $reference[$fc_info->name] = new ModuleInclusionMethod(
                        $fc_info->file, 
                        $fc_info->name, 
                        $g, 
                        $fc_info->line,
                        $fc_info->params,
                        $fc_info->code);
                }
                $fc_info->name = null;
                $fc_info->params = null;
                $fc_info->code = null;
            },


        ], $fc_handle ?? []);
        $v_is_debug = igk_is_debug(self::DEBUG_KEY);
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                $id = $e->tokenID;
                $v_is_debug && Logger::info('token:->' . $id . ' ' . $pos . ' ' . $e->value);
                if ($fc = igk_getv($fc_handle, $id)) {
                    $fc($e, $pos, $src);
                }
            }
        }

        // $hfile = fopen($file, 'r') ?? igk_die('can open a file');
        // $y = 0;

        // $regex->splittingDefinition = true;
        // while (!feof($hfile)) {
        //     $line = fgets($hfile);
        //     $y++;
        //     $pos = 0;
        //     $src = $line;
        //     // define
        //     while ($g = $regex->detect($src, $pos)) {
        //         if ($e = $regex->end($g, $src, $pos)) {
        //             $id = $e->tokenID;
        //             Logger::info('token:->' . $id . ' ' . $pos . ' ' . $e->value);
        //         }
        //     }
        // }
        // fclose($hfile);

        return $caches;
    }

    /**
    * auto generate doc.
    * @return
    */
    public static function InitRegexContainer()
    {
        $regex = new RegexMatcherContainer;
        $string = $regex->appendStringDetection('string', true)->last();
        $comments[] = $regex->appendSingleLineComment()->last();
        $comments[] = $regex->appendMultilineComment()->last();
        $heredoc = [];
        RegexMatcherUtility::AppendPhpHereDoc($regex, $heredoc);

        //$regex->begin('\\buse\\b', '(?<=;)', 'function-skip');
        $func = $regex->begin('\\bfunction\\b', '(?<=;|\})', 'function')->last();
        $func_code_block = $regex->createPattern([
            'begin' => '\{',
            'end' => '\}',
            'tokenID' => 'func-code-block'
        ]);
        $func_array_block = $regex->createPattern([
            'begin' => '\[',
            'end' => '\]',
            'tokenID' => 'func-array'
        ]);

        $func_param_block = $regex->createPattern([
            'begin' => '\(',
            'end' => '\)',
            'tokenID' => 'func-param'
        ]);
        $func_name = $regex->createPattern([
            'match' => '(&\\s*)?(?P<n>[a-zA-Z_][a-zA-Z_0-9]*)',
            'tokenID' => 'func-name'
        ]);
        $func_subblock[] = $func_subblock_array = $regex->createPattern([
            'begin' => '\[',
            'end' => '\]',
            'tokenID' => 'func-subblock-array'
        ]);
        $func_subblock[] = $func_subblock_parentheses = $regex->createPattern([
            'begin' => '\(',
            'end' => '\)',
            'tokenID' => 'func-subblock-parentheses'
        ]);
        $func_array_block->patterns = [
            $comments,
            $heredoc,
            $string,
            $func_subblock_array,
            $func_subblock_parentheses,
        ];
        $func_param_block->patterns = [
            $comments,
            $heredoc,
            $string,
            $func_subblock
        ];
        $func->patterns = [
            $comments,
            $heredoc,
            $func_name,            
            $func_param_block,
            $func_code_block
        ];

        return $regex;
    }
}
