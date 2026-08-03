<?php
// @file: IGKResourceUriResolver.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\Constants;
use IGK\Helper\IO;
use IGK\Helper\SysUtils;
use IGK\System\IO\Path;
use IGK\System\IO\ResIdentifierConstants;
use function igk_resources_gets as __;

/**
 * auto generate doc.
 */
class IGKResourceUriResolver
{
    /**
     * Property: environment.
     * @var mixed
     */
    private $environment;
    /**
     * Property: instance.
     * @var mixed
     */
    private static $sm_instance;
    /**
     * Flag: hash path.
     * @var mixed
     */
    private $m_hashPath;
    /**
     * Property: options.
     * @var mixed
     */
    private $m_options;
    /**
     * Constant: default mask.
     * @var mixed
     */
    const DEFAULT_MASK = IGK_DEFAULT_CACHE_FOLDER_MASK;
    /**
     * accept full uri resolution
     * @var bool
     */
    var $fulluri;
    /**
     * .ctr
     * @return mixed
     */
    private function __construct()
    {
        $this->fulluri = 0;
        $this->prepareEnvironment();
    }
    /**
     * mark path that need to be hashed before resolution
     * @param ?string $path
     */
    public function hashPath(?string $path = null)
    {
        $this->m_hashPath = $path ? $this->resolve($path, ["initHash" => 1]) : null;
    }
    /**
     * resolver instance
     * @return self
     */
    public static function getInstance()
    {
        if (self::$sm_instance == null) {
            self::$sm_instance = new IGKResourceUriResolver();
        }
        return self::$sm_instance;
    }
    /**
     * prepare uri resolution environment \ 
     * all public directory must have 775 mask 
     * @return void 
     */
    public function prepareEnvironment()
    {
        $app_dir = igk_io_applicationdir();
        $package_dir = igk_get_packages_dir();
        $this->environment = array(
            IGK_LIB_DIR . "/cgi-bin" => (object)array(
                "name" => "cgi-bin",
                "ini_chain" => function ($n, $rp) {
                    $chain = igk_uri(IGK_RES_FOLDER . "/_cgi_/" . $n);
                    $o = igk_io_basedir($chain);
                    $dir = dirname($o);
                    if (!igk_io_file_exists($o)) {
                        IO::CreateDir($dir, static::DEFAULT_MASK);
                        igk_io_symlink($rp, $o);
                    } else {
                        if (!is_link($o)) {
                            igk_die(__("res failed shortcut"));
                        }
                    }
                    if (!igk_io_file_exists($file = $dir . "/.htaccess")) {
                        igk_io_w2file($file, igk_io_read_allfile(IGK_LIB_DIR . "/Inc/default.cgi.htaccess"));
                    }
                    return $chain;
                }
            ),
            IGK_LIB_DIR => ResIdentifierConstants::LIBRARY,
            igk_get_module_dir() => ResIdentifierConstants::MODULE,
            igk_io_projectdir() => ResIdentifierConstants::PROJECT,
            $package_dir => ResIdentifierConstants::PACKAGE,
            $package_dir . '/node_modules' => ResIdentifierConstants::NODE_PACKAGE,
            igk_io_cachedir() => ResIdentifierConstants::CACHE
        );
        $public_asset = Path::getInstance()->getPublicAssetDir();
        if (($c = $app_dir . "/Lib/igk") != IGK_LIB_DIR) {
            $this->environment[$c] = ResIdentifierConstants::LIBRARY;
        }
        krsort($this->environment, SORT_REGULAR);
        $_access = implode("\n", ["allow from all", "AddType text/javascript js", "AddEncoding deflate js", "<IfModule mod_headers.c>", "Header set Cache-Control \"max-age=31536000\"", "</IfModule>",]);
        if ($c =  $public_asset . "/_chs_/dist/js/.htaccess") {
            igk_io_w2file($c, $_access, false);
        }
        if ($c =  $public_asset . "/dist/js/.htaccess") {
            igk_io_w2file($c, $_access, false);
        }
    }
    /**
     * auto generate doc.
     * @param mixed $j
     * @param mixed $n
     * @param mixed $options
     * @return mixed
     */
    private function __hashResPath($j, $n, $options)
    {
        $chain = igk_uri(IGK_RES_FOLDER . "/" . $j . "/" . $n);
        if (!is_null($this->m_hashPath) && igk_getv($options, "hashed")) {
            if (strpos($chain, $this->m_hashPath) === 0) {
                $dir = substr($chain, strlen($this->m_hashPath) + 1);
                $v_path = substr($chain, strlen(IGK_RES_FOLDER . "/" . $j), strlen($this->m_hashPath) - strlen(IGK_RES_FOLDER . "/" . $j));
                $chain = implode("/", array_filter([IGK_RES_FOLDER, $j, sha1($v_path), $dir]));
            }
        }
        return $chain;
    }
    /**
     * get resource base path
     * @param mixed $path
     */
    private function _getResPath($path): ?string
    {
        if ($g = preg_match($rgx = Constants::PATH_VAR_DETECT_MODEL_REGEX, $path, $tab)) {
            $s = preg_replace($rgx, '', $path);
            $n = $tab['name'];
            return Path::Combine(IGK_RES_FOLDER, igk_getv([
                'lib' => ResIdentifierConstants::LIBRARY,
                'mod' => ResIdentifierConstants::MODULE,
                'modules' => ResIdentifierConstants::MODULE,
                'pkg' => ResIdentifierConstants::PACKAGE,
                'nodepackages' => ResIdentifierConstants::NODE_PACKAGE,
                'packages' => ResIdentifierConstants::PACKAGE,
                'prj' => ResIdentifierConstants::PROJECT,
                'project' => ResIdentifierConstants::PROJECT,
                'cache' => ResIdentifierConstants::CACHE,
                'app' => ResIdentifierConstants::APP
            ], $n, function () use ($n, $path) {
                igk_die("not found .... " . $n . " for [" . $path . "]");
            }), $s);
        }
        return null;
    }
    /**
     * resolve path
     * @param mixed $path path to resolve
     * @param mixed $options hashed| to hash path key     
     * @return null|string 
     * @throws IGKException 
     */
    public function resolve(string $path, $options = null): ?string
    {
        static $appData = null;
        if (empty($path))
            return null;
        $fulluri = $this->fulluri || igk_is_ajx_demand();
        $this->m_options = $options;
        $buri = explode("?", $path);
        $path = $buri[0];
        $query = "";
        $rp = "";
        if (count($buri) > 1) {
            $query = "?" . implode("?", array_slice($buri, 1));
        }
        $bdir = igk_io_basedir();
        $path = igk_uri($path);
        if (igk_io_is_subdir($bdir, $path)) {
            $n_uri = igk_html_get_system_uri($path, $options) . $query;
            return $n_uri;
        }
        if (IO::IsRealAbsolutePath($path)) {
            $rp = igk_realpath($path);
            if (!igk_io_is_subdir($bdir, $rp)) {
                $acpath = igk_io_access_path($rp);
                if (!igk_io_is_subdir($bdir, $path)) {
                    $rp = $acpath;
                }
                if (is_null($rp)) {
                    igk_dev_wln_e(
                        __FILE__ . ":" . __LINE__,
                        "missing rp " . $path,
                        $rp,
                        realpath($path),
                        $acpath,
                        igk_realpath($path)
                    );
                }
                return $this->resolveResource($rp, $fulluri) . $query;
            }
        }
        if ($appData === null) {
            $i = 1;
            if (!strstr(IGK_LIB_DIR, igk_uri(igk_io_applicationdir())))
                $i = 0;
            $appData = $i;
        }
        if (!$appData && ((strpos(igk_uri($path), IGK_LIB_DIR)) === 0)) {
            $v = ltrim(substr($path, strlen(IGK_LIB_DIR)), "/");
            return igk_html_get_system_uri('') . ltrim(igk_io_libdiruri($rp, $options) . $v . $query, '/');
        }
        if (($v = igk_ajx_link($path)) == null) {
            $v = igk_html_get_system_uri($path, $options);
        }
        return $v . $query;
    }
    const PRJ_FOLDER = ResIdentifierConstants::PROJECT;
    /**
     * 
     * @param mixed $s 
     * @return mixed 
     */
    public function treatProjectAssetPath($s)
    {
        if (false !== (strpos($s, '/Data/'))) {
            $p = sprintf('/%s/', self::PRJ_FOLDER);
            $dpos = strpos($s, $p) + +strlen($p);
            $match = substr($s, $dpos);
            $tab = explode('/', $match, 4);
            $last = array_pop($tab);
            $d = implode('/', array_slice($tab, 0, 3));
            $d = hash('crc32b', $d);
            $s = Path::Combine(substr($s, 0, $dpos), $d, $last);
        }
        return $s;
    }
    /**
     * resolve resources uri
     * @param string $rp path 
     * @param bool $fulluri path 
     * @return ?string
     */
    public function resolveResource(string $rp, bool $fulluri = false): ?string
    {
        $v_cpath = igk_io_collapse_path($rp);
        $v_res_path = $this->_getResPath($v_cpath) ?? igk_die('res path is null : '.$rp);
        if (igk_environment()->isOPS() && strstr($v_res_path, sprintf('/%s/', self::PRJ_FOLDER))) {
            $v_res_path = $this->treatProjectAssetPath($v_res_path);
        }
        $v_bdir = igk_io_basedir();
        if (!igk_io_file_exists($fc = Path::Combine($v_bdir, $v_res_path), true)){
            // + | missing - create a link to 
            if (!igk_io_symlink($rp, $fc)) {
                // igk_ilog(
                //     __("Failed to create symbolic link - 2 - ") . " " . $rp . '==$gt; ' . $fc . " ? " . is_link($fc)
                // );
                return null;
            }
            // igk_debug_wln("generate new link . " . $fc); 
            igk_hook(IGKEvents::HOOK_MK_LINK, array("outdir" => dirname($fc), "link" => $fc));
        }
        $relative = Path::GetRelativePath($v_bdir, $fc);
        $li = $this->transform($relative, $fulluri);
      
        return $li;
    }
    /**
     * 
     * @param string $relative 
     * @param bool $fulluri 
     * @return string|mixed 
     */
    function transform(string $relative,bool  $fulluri)
    {
        if ($fulluri) {
            return Path::FlattenPath(igk_io_baseuri() . '/' . $relative);
        }
        return igk_io_currentrelativeuri($relative);
    }
    /**
     * Resolves Full Uri.
     * @param mixed $uri
     */
    public function resolveFullUri($uri)
    {
        $data = $this->resolve($uri);
        while (strpos($data, "../") === 0) {
            $data = substr($data, 3);
        }
        return igk_io_baseuri() . "/" . $data;
    }
    /**
     * Resolves Only.
     * @param string $file
     * @param mixed & $notresolved
     */
    public function resolveOnly(string $file, &$notresolved = 0)
    {
        $fulluri = $this->fulluri || igk_is_ajx_demand();
        $notresolved = 0;
        $bdir = igk_uri(igk_io_basedir());
        $rp = igk_uri($file);
        $options = null;
        $uri = "";
        if (!igk_io_is_subdir($bdir, $rp)) {
            $tab = $this->environment;
            foreach ($tab as $i => $j) {
                if (igk_io_is_subdir($i, $rp)) {
                    $s = $j;
                    $n = substr($rp, strlen($i) + 1);
                    if (is_object($s)) {
                        $b = $s->{'ini_chain'};
                        $chain = $b($n, $rp);
                    } else {
                        $chain = igk_uri(IGK_RES_FOLDER . "/" . $j . "/" . $n);
                    }
                    if ($fulluri)
                        return igk_io_baseuri($chain);
                    return igk_io_currentrelativeuri($chain, null);
                }
            }
            $gs_uri = igk_html_get_system_uri($uri, $options);
            if ($gs_uri) {
                $gs_uri = preg_replace("#(\.\./)+#", "_oth_/", $gs_uri);
                $chain = igk_uri(IGK_RES_FOLDER . "/" . $gs_uri);
                $outlink = null;
                if ($fulluri) {
                    $outlink = igk_io_baseuri($chain);
                } else {
                    $outlink = igk_io_currentrelativeuri($chain, $options);
                }
                return $outlink;
            }
        }
        $notresolved = 1;
        if (($v = igk_ajx_link($rp)) == null)
            $v = igk_html_get_system_uri($rp, $options);
        return $v;
    }
}
