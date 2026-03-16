<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ModuleInitializer.php
// @date: 20220829 09:55:54
// @desc: 
namespace IGK\System\Modules;

use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;

/**
 * initializer modules
 * @package IGK\System\Modules
 */
class ModuleInitializer
{

    /**
     * Property: modules.
     * @var mixed
     */
    protected $m_modules = [];

    /**
     * Resets.
     */
    public function reset()
    {
        $this->m_modules = [];
    }

    /**
     * Returns.
     * @param string $path
     */
    public function get(string $path)
    {
        return igk_getv($this->m_modules, $this->_get_key($path));
    }

    /**
     * Registers.
     * @param mixed $path
     * @param mixed $module
     */
    public function register($path, $module)
    {
        $this->m_modules[$this->_get_key($path)] = $module;
    }

    /**
     * Get key.
     * @param string $path
     */
    protected function _get_key(string $path)
    {
        return "sys://modules/" . strtolower(str_replace("/", ".", igk_uri($path)));
    }

    /**
    * auto generate doc.
    * @return void
    */
    public static function Init($module, $file, &$reference)
    {
        //$hashfile = 'modules/'.hash_file('sha256', $file);
        $hashfile = 'modules/' . hash_file('crc32b', $file) . '.json';
        $v_syscache = igk_cache();
        if ($v_syscache->file_exists($hashfile)) {
            $data = $v_syscache->get($hashfile);
            $r = (array)json_decode($data);
            if (isset($r['cache'])) {
                foreach ($r['cache'] as $k => $v) {
                    if (empty($v->name)){
                        $v->name = $k;
                    }
                    $method = ModuleInclusionMethod::WakeUpFromCache($v);
                    $reference[$k]=$method;
                } 
            } 
            return $r;
        }
        $src = file_get_contents($file);
        $code = $return = null;
        $cache = ModuleIncludeDefinitionUtility::BindSourceFile($src, $file, $reference);
        self::_LoadCode($src, $code, $return);
        $_ret = compact('return', 'code', 'cache');
        $v_syscache->store($hashfile, json_encode($_ret));
        return $_ret;
    }

    /**
    * auto generate doc.
    * @param string $src
    * @param mixed & $code
    * @param mixed & $return
    * @return
    */
    private static function _LoadCode(string $src, &$code, &$return)
    {
        // split file code 
        $regex = new RegexMatcherContainer;
        $innerdef[] = $regex->appendMultilineComment()->last();
        $innerdef[] = $regex->appendSingleLineComment()->last();
        $regex->autoStore = false;
        $innerdef[] = $regex->appendStringDetection('string', true)->last();
        RegexMatcherUtility::AppendPhpHereDoc($regex, $innerdef);
        $regex->autoStore = true;

        $v_block = $regex->begin('\{', '\}', 'block')->last();
        $regex->begin('\\buse\\b', '(?<=;|\\})', 'use-skip')->last();

        $v_func = $regex->begin('\\bfunction\\b', '(?<=;|\\})', 'module-function')->last();
        $v_return = $regex->begin('\\breturn\\b', ';', 'module-return')->last();
        $func_name = $regex->createPattern([
            'match' => '(&\\s*)?(?P<n>[a-zA-Z_][a-zA-Z_0-9]*)',
            'tokenID' => 'func-name'
        ]);
        $func_subblock_parentheses = $regex->createPattern([
            'begin' => '\(',
            'end' => '\)',
            'tokenID' => 'func-subblock-parentheses'
        ]);
        $brank =  $regex->createPattern([
            'begin' => '\[',
            'end' => '\]',
            'tokenID' => 'func-subblock-brank'
        ]);
        $v_return->patterns = [
            $innerdef
        ];
        $brank->patterns = $func_subblock_parentheses->patterns = [
            $innerdef,
            $brank,
            $func_subblock_parentheses
        ];
        $v_func->patterns = [
            $func_name,
            $func_subblock_parentheses,
            $v_block,
            $innerdef
        ];
        $v_block->patterns = [
            $v_block
        ];
        $pos = 0;
        $v_defobject = (object)[
            'return' => &$return,
            'replaces' => [],
            'func_name' => null,
            'anonymous'=>null,
        ];
        // define
        $fc_handle = [
            'module-return' => function ($e) use ($v_defobject) {
                $v_defobject->return  = $e->value;
                $v_defobject->replaces[] = (object)['from' => $e->from, 'to' => $e->to, 's' => ''];
            },
            'func-name' => function ($e) use ($v_defobject) {
                if (!$v_defobject->anonymous){
                    $v_defobject->func_name = $e->value;
                }else{
                    $v_defobject->func_name = null;
                }

            },
            'func-subblock-parentheses' => function ($e) use ($v_defobject) {
                $v_defobject->anonymous = 1;
            },
            'module-function' => function ($e) use ($v_defobject) {
                if ($v_defobject->func_name) {
                    //skip 
                    $v_defobject->replaces[] = (object)['from' => $e->from, 'to' => $e->to, 's' => ''];
                }
                $v_defobject->func_name = null;
                $v_defobject->anonymous = null;
            }
        ];




        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                $id = $e->tokenID;
                //igk_wln("tokenid : ".$id);
                if ($fc = igk_getv($fc_handle, $id)) {
                    $fc($e, $pos, $src);
                }
            }
        }
        $rep = &$v_defobject->replaces;
        usort($rep, function ($a, $b) {
            return $a->from <=> $b->from;
        });
        $code = '';
        $offset =  0;
        while (count($rep) > 0) {
            $q = array_shift($rep);
            $ln = $q->from - $offset;
            $code .= substr($src, $offset, $q->from - $offset) . $q->s;
            $offset = $q->to;
        }
        $code .= substr($src, $offset);
    }
}
