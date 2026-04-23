<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleIncludeDefinitionUtility
// @date: 20260228 13:59:29
namespace IGK\System\Modules;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilderUtility;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;
use function igk_resource_gets_map;

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
    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    const DEBUG_KEY = 'debug-module-include-utility';
    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    const SELF_REFERENCE = '_this';
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
     * @param string $param
     * @param mixed $code
     * @param string $selfKey
     * @return
     */
    public static function CreateMethodHandle(
        string $param,
        $code,
        string $selfKey = self::SELF_REFERENCE,
        ?string $namespace = null,
        ?array $uses = null,
        ?string $in_condition = null
    ) {
        if (isset($in_condition)) {
            $code = sprintf('if %s{%s}', $in_condition, $code);
        }
        if ($namespace || $uses) {
            $ns = $namespace ? 'namespace ' . $namespace . ';' : '';
            $t = $uses ? implode("\n", $uses) : "";
            $code = $ns . $t . $code;
        }
        $fc = (function ($param, $code, $selfKey) {
            $__def = [
                $param,
                $code,
                'invoke' => function () {
                    extract(func_get_arg(0));
                    try {
                        return @eval(func_get_arg(1));
                    } catch (\Error $ex) {
                        igk_dev_wln("error::::", func_get_arg(1), array_keys(get_defined_vars()), $ex->getMessage());
                        throw $ex;
                    } catch (\Exception $ex) {
                        if (igk_environment()->isDev()) {
                            Logger::danger('error: ' . $ex->getMessage());
                            igk_wln("Exception: ", func_get_arg(1));
                        }
                        throw $ex;
                    }
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
     * @return array|object info to caches
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
     * @return array|object array if only function or object that contain namespace | uses | caches 
     */
    public static function BindSourceFile(string $source, string $file, ?array &$reference, ?array $fc_handle = [], ?string $refkey = self::SELF_REFERENCE)
    {
        $func_list = [];    
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
            'code' => null,
            'namespace' => null,
            'uses' => [],
            'conditions' => [],
            'func_list' => &$func_list,
            'elseconditions' => [],
        ];
        $fc_handle = array_merge([
            'root-condition' => function ($e) use ($fc_info) {
                $type = igk_conf_get($e->beginCaptures, 'type/0');
                if ($type == 'else') {
                    $tc = $fc_info->func_list;
                    $cond = sprintf('(!(%s))', implode(' && ', $fc_info->elseconditions));
                    while (count($tc)) {
                        $q = array_pop($tc);
                        if ($q->conditions)
                            break;
                        $q->conditions = [$cond];
                    }
                    $fc_info->elseconditions = [];
                }
                $fc_info->elseconditions = array_merge($fc_info->elseconditions, $fc_info->conditions);
                $fc_info->conditions = [];
            },
            'condition' => function ($e) use ($fc_info) {
                $fc_info->conditions[] = $e->value;
            },
            'namespace' => function ($e) use ($fc_info, &$caches) {
                $fc_info->namespace = igk_conf_get($e->beginCaptures, 'n/0');
            },
            'use' => function ($e) use ($fc_info, &$caches) {
                $fc_info->uses[] = $e->value;
            },
            'func-name' => function ($e) use ($fc_info) {
                if (is_null($fc_info->name) && !is_null($fc_info->params)) {
                    $fc_info->name = null;
                    $fc_info->params = null;
                    return;
                }
                if ($fc_info->name) {
                    if ('use' == trim($e->value)) {
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
            'function' => function ($e) use ($fc_info, &$reference, &$caches, &$func_list) {
                if ($fc_info->name && $fc_info->code) {
                    $func_list[] =
                        igk_extract_obj($fc_info, 'name|file|line|params|code|conditions');
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
        foreach ($func_list as $c) {
            $g = self::CreateMethodHandle(
                $c->params ?? '',
                $c->code,
                $refkey,
                $fc_info->namespace,
                $fc_info->uses,
                $c->conditions ? implode(' && ', $c->conditions) : null
            );
            $caches[$c->name] = $reference[$c->name] = new ModuleInclusionMethod(
                $c->file,
                $c->name,
                $g,
                $c->line,
                $c->params,
                $c->code
            );
        }
        if ($caches && (($fc_info->namespace) || $fc_info->conditions || ($fc_info->uses))) {
            return igk_createobj(array_filter([
                'namespace' => $fc_info->namespace,
                'uses' => $fc_info->uses,
                'conditions' => $fc_info->conditions,
                ModuleInitializer::CACHE_DEF => $caches
            ]));
        }
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
        $root_condition = $regex->begin('\\b(?P<type>if|else|elseif)\\b', '(?<=,|\})', 'root-condition')->last();
        $ns_def = $regex->begin('\\bnamespace\\b\\s*(?P<n>[a-zA-Z][a-zA-Z0-9_]*(\\\\[a-zA-Z][a-zA-Z0-9_]*)*)', '(?<=;|\})', 'namespace')->last();
        $ns_block =  $regex->createPattern([
            'begin' => '\{',
            'end' => '\}',
            ''
        ]);
        $ns_usedef = $regex->begin('\\buse\\b', '(?<=;)', 'use')->last();
        $ns_usedef->patterns = [
            $comments,
        ];
        $ns_def->patterns = [
            $comments,
            $ns_block,
        ];
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
        $func_code_block->patterns = [
            $comments,
            $heredoc,
            $func_code_block
        ];
        $ns_block->patterns = [
            $comments,
            $heredoc,
            $func
        ];
        $cond = $regex->createPattern([
            'begin' => '\(',
            'end' => '\)',
            'tokenID' => 'condition'
        ]);
        $subcond_block = $regex->createPattern([
            'begin' => '\(',
            'end' => '\)',
            'tokenID' => 'sublock'
        ]);
        $subcond_block->patterns = [
            $string,
            $comments,
            $heredoc,
            $subcond_block
        ];
        $cond->patterns = [
            $string,
            $comments,
            $heredoc,
            $subcond_block
        ];
        $root_condition->patterns = [
            $comments,
            $heredoc,
            $cond,
            $func
        ];
        return $regex;
    }
}