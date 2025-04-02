<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlScriptLoader.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

use IGK\Core\Traits\ScriptTrait;
use IGK\Helper\IO;
use IGK\System\Exceptions\NotImplementException;
use IGK\System\Html\HtmlRenderer;
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;
use IGK\System\Regex\Replacement;
use IGKCaches;
use IGKException;
use IGKResourceUriResolver;

/**
 * script loader 
 * @package IGK\System\Html\Dom
 */
class HtmlScriptLoader
{
    use ScriptTrait;

    var $options;

    /**
     * directory to load
     * @var mixed
     */
    var $dirs;

    /**
     * production mode
     * @var bool
     */
    var $production;

    /**
     * 
     * @var ?array excluded directory options
     */
    var $excludir;

    public function getExcludeDir(): array
    {
        return $this->excludir ? $this->excludir : igk_sys_js_exclude_dir();
    }


    public function getscript($options = null)
    {
        return self::LoadScripts($this->dirs, $options, $this->production, $this->getExcludeDir());
    }

    /**
     * load script 
     * @param array $tab array of directory 
     * @param mixed $options render option
     * @param bool $production production mode 
     * @param array $exclude_dir list of excluded directory
     * @param string $cachePath cache path
     * @return string|false result
     * @throws IGKException 
     */
    public static function LoadScripts($tab, $options = null, $production = false, $exclude_dir = [], $cachePath = "corejs:/igk.js", $defer = 0)
    {
        $no_page_cache = igk_setting()->no_page_cache();
        $out = "";
        $uri = igk_server()->REQUEST_URI ?? "";
        $resolver = IGKResourceUriResolver::getInstance();
        $firstEval = $options ? igk_getv($options, "jsOpsFirstEval", true) : true;
        $references = [];

        if ($options && $firstEval)
            $options->jsOpsFirstEval = false;

        // 
        // default library directory             
        // append script to ignore
        //       
        $d = rtrim(explode("?", $uri)[0], "/");
        $rq = null;
        $resolverfc = null;
        $tag = null;
        $s = "";
        $lf = $options ? $options->LF : "";
        $tabstop = HtmlRenderer::GetTabStop($options);
        $production_file  = "";
        if (!$production) {
            $to_check_loading = [];
            $v_loaded = [];
            $rq = count(array_filter(explode("/", $d))) . "/:";
            $resolverfc = function ($f) use ($resolver, &$s, &$tag, $lf, $tabstop, $defer, &$to_check_loading, &$v_loaded) {
                $g = basename($f);
                $check_dir = null;
                if (strpos($g, ".") === 0) {
                    // + | ignore hidden file
                    return;
                }
                if (!isset($to_check_loading[$check_dir = dirname($f)])) {
                    $lib = $check_dir . '/__autoload.json';
                    $inf = (object)['required' => [], 'ignore' => []];
                    if (file_exists($lib)) {
                        $jsdata = json_decode(file_get_contents($lib));
                        if ($jsdata) {
                            list($required, $ignore) = igk_extract($jsdata, 'required|ignore');
                            // file need to bee loaded before requirement 
                            $sep = DIRECTORY_SEPARATOR;
                            if ($required)
                                foreach ($required as $k => $v) {
                                    $_o = [];
                                    //file to load before 
                                    foreach ($v as $tf) {
                                        $_o[] = Path::CombineAndFlattenPath($check_dir, $tf);
                                    }
                                    $inf->required[$check_dir . $sep . $k] = $_o;
                                }
                            if ($ignore) {
                                foreach ($ignore as $v) {
                                    $inf->ignore[] = Path::CombineAndFlattenPath($check_dir, $v);
                                }
                            }
                        }
                    }
                    $to_check_loading[$check_dir] = $inf;
                }
                $inf = $to_check_loading[$check_dir];
                // + | auto ignore definition js but resolved
                if (!$inf->ignore && preg_match("/\.d\.js$/", $f)) {
                    $inf->ignore[] = $f;
                }
                $loading = [$f];
                if ($of = igk_getv($inf->required, $f)) {
                    array_unshift($loading, ...$of);
                }
                while (count($loading) > 0) {
                    $f = array_shift($loading);
                    if (isset($v_loaded[$f])) {
                        // igk_ilog(__FILE__.":".__LINE__ . " : already loaded : ".$f);
                        continue;
                    }
                    $ext = Path::GetExtension($f);
                    $u = $resolver->resolve($f);
                    if ($inf->ignore && (array_search($f, $inf->ignore) !== false)) {
                        // resolve be not load at start 
                        continue;
                    }
                    switch (($ext)) {
                        case ".js";
                            $u .= "?v=" . IGK_VERSION;
                            $s .= $tabstop . "<script type=\"text/javascript\" language=\"javascript\" src=\"{$u}\"";
                            $is_core = (($tag == "igk") && (basename($f) == "igk.js"));
                            $defer = $defer || !$is_core; // (($tag=="igk" ) && (basename($f) != "igk.js"));
                            if ($defer) {
                                $s .= " defer";
                            }
                            $s .= " ></script>" . $lf;
                            break;
                    }
                }
            };
        } else {
            $production_file = IGKCaches::js_filesystem()->getCacheFilePath($cachePath, ".js");
            if (!$no_page_cache  && file_exists($production_file)) {
                return file_get_contents($production_file);
            }

            $assets = [];
            $resolverfc = function ($f) use (&$s, &$assets, &$references) {
                if (strpos(basename($f), '.') === 0) {
                    return;
                }
                $ext = Path::GetExtension($f);
                $F = igk_io_collapse_path($f);
                switch (($ext)) {
                    case ".js";
                        $s .= IGK_START_COMMENT . "F: " . $F . "" . IGK_END_COMMENT . IGK_LF;
                        $ts = file_get_contents($f);
                        $ts = self::TreatJSSource($f, $ts, $references);
                        $s .= $ts;
                        break;
                    default:
                        //resolv to asset folder
                        $assets[] = $f;
                        break;
                }
            };
        }

        while ($q = array_shift($tab)) {
            $dir = $q[0];
            $tag = $q[1];
            if ($dir && key_exists($dir, $exclude_dir)) {
                continue;
            }

            $cache_path = IGKCaches::js_filesystem()->getCacheFilePath($rq . $dir);
            if (!$no_page_cache && file_exists($cache_path)) {
                ob_start();
                include($cache_path);
                $out .= ob_get_contents();
                ob_end_clean();
            } else {
                // + | load and project file 
                $s = "";
                $dirs = [];
                $dirs[] = $dir . "/igk.js";
                $dirs[] = $dir . "/polyfill.js";
                $dirs[] = $dir . "/system/ctrl/ctrl.js";
                $exclude_dir += array_fill_keys($dirs, 1);
                IO::GetFiles($dir, self::GetLoadingAssetRegex(), true, $exclude_dir, $resolverfc);

                // store references 
                if ($references) {



                    $sb = new StringBuilder;
                    $t = 'const a = __module_refs;';
                    $id = 0;
                    foreach (array_keys($references) as $k) {
                        // do not end with single comment line break definition 
                        $t .= sprintf('a[' . $id . ']= %s;', self::ImportContentAsModule(file_get_contents($k)));
                        $id++;
                    }
                    // + | remove use strict 
                    $rp = new Replacement;
                    $rp->add('/(\'|")use\\s+strict(s)?\\s*(\\1)(;)?(\\s+)?/', '');
                    $s = $rp->replace($s);

                    $sb->append('\'use strict\';');
                    $sb->append(sprintf(
                        '(function(window){ const __module_refs = []; (()=>{%s; return (id)=>{return a[id].apply();};})(); %s})(window);',
                        $t,
                        $s
                    ));
                    $s = $sb . '';
                    $references = [];
                }
                IO::WriteToFile($cache_path, $s);
                $out .= $s;
            }
        }
        if ($production && !empty($out)) {
            $pif = [
                igk_js_minify($out),
                $firstEval ? igk_js_minify(file_get_contents(IGK_LIB_DIR . "/Inc/js/eval.js")) : "igk.js.initEmbededScript()"
            ];
            $out = $tabstop . "<script type=\"text/javascript\" language=\"javascript\" >\n//<![CDATA[" . $pif[0] . "]]>\n</script>" . $lf;
            $out .= $tabstop . "<script type=\"text/javascript\" language=\"javascript\" >\n" . $pif[1] . "\n</script>" . $lf;
            if (!$no_page_cache) {
                IO::WriteToFile($production_file, $out);
            }
        }
        $r = preg_match_all("/igk.control.js/", $out, $tab);
        if ($r > 1) {
            igk_wln_e(__FILE__ . ":" . __LINE__, 'error loading file twice', sprintf('%s', json_encode($tab, JSON_PRETTY_PRINT)), $out);
        }
        return $out;
    }
    /**
     * import content as module 
     * @param string $content script with module 
     */
    public static function ImportContentAsModule(string $content): string
    {
        return implode("\n", [
            'function(){',
            'const module = {};',
            $content,
            'return {default: module.exports, ...module.exports};',
            '}'
        ]);
    }
    /**
     * system loading accept regex 
     * @return string 
     */
    public static function GetLoadingAssetRegex()
    {
        return "/\.((m)?js|json|xml|svg|shader|txt)$/";
    }

    /**
     * 
     * @param string $file 
     * @param string $uri 
     * @return string 
     * @throws IGKException 
     */
    public static function GetModuleInlineScriptContent(string $file, $uri = "/")
    {
        $sb = new StringBuilder;
        $sb->appendLine("(function(){");
        $mod_info = [
            "path" => igk_io_collapse_path($file),
            "uri" => $uri
        ];
        $sb->appendLine("const __MODULE__ = " . json_encode((object)$mod_info, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . ";");
        $sb->appendLine(file_get_contents($file));
        $sb->appendLine("})();");
        return "" . $sb;
    }

    /**
     * get core script exception
     * @return never 
     * @throws NotImplementException 
     */
    public static function GetCoreScriptInlineContent()
    {
        throw new NotImplementException(__METHOD__);
    }
}
