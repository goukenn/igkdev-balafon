<?php
// @file: IGKResourceUriResolver.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\Helper\IO;
use IGK\Helper\SysUtils;
use IGK\System\IO\Path;

use function igk_resources_gets as __;

/**
 * 
 * @package 
 */
class IGKResourceUriResolver
{
    private $environment;
    private static $sm_instance;
    private $m_hashPath;
    private $m_options;
    /**
     * accept full uri resolution
     * @var bool
     */
    var $fulluri;
    ///<summary></summary>
    private function __construct()
    {
        $this->fulluri = 0;
        $this->prepareEnvironment();
    }

    /**
     * mark path that need to be hashed before resolution
     */
    public function hashPath(?string $path = null)
    {
        $this->m_hashPath = $path ? $this->resolve($path, ["initHash" => 1]) : null;
    }
    ///<summary>get resolver instance</summary>
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
    ///<summary>utility use to bind javascript resources
    public function prepareEnvironment()
    {
        $this->environment = array(
            IGK_LIB_DIR . "/cgi-bin" => (object)array(
                "name" => "cgi-bin",
                "ini_chain" => function ($n, $rp) {
                    $chain = igk_uri(IGK_RES_FOLDER . "/_cgi_/" . $n);
                    $o = igk_io_basedir($chain);
                    $dir = dirname($o);
                    if (!file_exists($o)) {
                        IO::CreateDir($dir);
                        igk_io_symlink($rp, $o);
                    } else {
                        if (!is_link($o)) {
                            igk_die(__("res failed shortcut"));
                        }
                    }
                    if (!file_exists($file = $dir . "/.htaccess")) {
                        igk_io_w2file($file, igk_io_read_allfile(IGK_LIB_DIR . "/Inc/default.cgi.htaccess"));
                    }
                    return $chain;
                }
            ),
            IGK_LIB_DIR => "_lib_",
            igk_get_module_dir() => "_mod_",
            igk_io_projectdir() => "_prj_",
            igk_get_packages_dir() => "_pkg_",
            igk_io_cachedir() => "_chs_"
        );
        krsort($this->environment, SORT_REGULAR);
        $_access = implode("\n", ["allow from all", "AddType text/javascript js", "AddEncoding deflate js", "<IfModule mod_headers.c>", "Header set Cache-Control \"max-age=31536000\"", "</IfModule>",]);
        if (!file_exists($c = igk_io_basedir() . "/assets/_chs_/dist/js/.htaccess")) {
            igk_io_w2file($c, $_access);
        }
        if (!file_exists($c = igk_io_basedir() . "/assets/dist/js/.htaccess")) {
            igk_io_w2file($c, $_access);
        }
    }
    private function __hashResPath($j, $n, $options){
        $chain = igk_uri(IGK_RES_FOLDER . "/" . $j . "/" . $n);
        if (!is_null($this->m_hashPath) && igk_getv($options, "hashed")) {
            if (strpos($chain, $this->m_hashPath) === 0) {
                $dir = substr($chain, strlen($this->m_hashPath)+1);
                $v_path = substr($chain, strlen(IGK_RES_FOLDER . "/" . $j ), strlen($this->m_hashPath) - strlen(IGK_RES_FOLDER . "/" . $j ));
                 
                // hash no 
                $chain = implode("/", array_filter([IGK_RES_FOLDER , $j , sha1($v_path), $dir]));
                
            }
        }
        return $chain;
    }
    ///<summary>resolve existing file to asset resources</summary>
    /**
     * resolve uri
     * @param mixed $uri 
     * @param mixed $options hashed| to hash path key
     * @param int $generate 
     * @return null|string 
     * @throws IGKException 
     */
    public function resolve(string $uri, $options = null, $generate = 1)
    {
        static $appData = null;
        if (empty($uri))
            return null;
        $fulluri = $this->fulluri || igk_is_ajx_demand();
        $tab = $this->environment;
        $initHash = igk_getv($options, "initHash");
        $this->m_options = $options;
        $chainRes = function ($rp, $j, $n, &$chain) use ($generate, $options) {
            $options = $this->m_options;
            $chain = $this->__hashResPath($j, $n, $options);
            //  igk_uri(IGK_RES_FOLDER . "/" . $j . "/" . $n);
            // if (!is_null($this->m_hashPath) && igk_getv($options, "hashed")) {
            //     if (strpos($chain, $this->m_hashPath) === 0) {
            //         $dir = substr($chain, strlen($this->m_hashPath)+1);
            //         $v_path = substr($chain, strlen(IGK_RES_FOLDER . "/" . $j ), strlen($this->m_hashPath) - strlen(IGK_RES_FOLDER . "/" . $j ));
            //         if (strstr($this->m_hashPath, $chain)) {
            //             // hash no 
            //             $chain = implode("/", array_filter([IGK_RES_FOLDER , $j , sha1($v_path), $dir]));
            //         }
            //     }
            // }
            $o = igk_io_basedir($chain);
            if ($generate) {
                if (!file_exists($o) && !is_link($o)) {
                    $odir = dirname($o);
                    if (IO::CreateDir($odir)) {
                        
                        $rp = SysUtils::ResolvLinkPath($rp);
                        if (!file_exists($o) && !($outlink = igk_io_symlink($rp, $o))) {
                            igk_ilog(__("Failed to create symbolic link - 2 - ") . " " . $rp . '==$gt; ' . $o . " ? " . is_link($o) . " = " . $outlink);
                            return null;
                        }
                        igk_hook("generateLink", $gt = array("outdir" => $odir, "link" => $rp));
                    } else {
                        igk_ilog("failed to create dir:" . $odir);
                        return null;
                    }
                }
            }
        };
        $createlink = function ($target, $cibling) use ($generate) {
            if (file_exists($cibling) && !$generate) {
                return 1;
            }
            $outlink = 1;
            if (!file_exists($cibling) && !($outlink = igk_io_symlink($target, $cibling))) {
                igk_die(__("Failed to create symbolic link -1- {0} ==&gt; {1}", $target, igk_realpath($target), $cibling));
            }
            igk_hook("generateLink", array(
                "source" => $target,
                "outdir" => dirname($cibling),
                "link" => $cibling
            ));
            return $outlink;
        };
        $buri = explode("?", $uri);
        $uri = $buri[0];
        $query = "";
        $rp = "";
        if (count($buri) > 1) {
            $query = "?" . implode("?", array_slice($buri, 1));
        }
        $bdir = igk_io_basedir();
        $uri = igk_uri($uri);


        if (igk_io_is_subdir($bdir, $uri)) {
            $n_uri = igk_html_get_system_uri($uri, $options) . $query;
            return $n_uri;
        }
        if (IO::IsRealAbsolutePath($uri)) {
            $rp = igk_realpath($uri);
            // igk_wln_e(':-)', '<----->', $bdir, $uri, igk_io_collapse_path($rp), Path::LocalPath($bdir));
            if (!igk_io_is_subdir($bdir, $rp)) {
                $acpath = igk_io_access_path($rp);
                if (!igk_io_is_subdir($bdir, $uri)) {
                    $rp = $acpath;
                }
                // resolve path in other to store access to link
                $v_found = false;
                foreach ($tab as $i => $j) {
                    if (strstr($rp, $i)) {

                        $chain = "";
                        $chainRes($rp, $j, substr($rp, strlen($i) + 1), $chain, $generate);
                        $v_found = true;
                    } else {
                        if (($s = realpath($i)) && ($s != $i)) {
                            $i = $s;
                        }
                        if (igk_io_is_subdir($i, $rp)) {
                            $s = $j;
                            $n = substr($rp, strlen($i) + 1);
                            $v_found = true;
                            if (is_object($s)) {
                                $b = $s->{'ini_chain'};
                                $chain = $b($n, $rp);
                            } else {
                                $chain = "";
                                $chainRes($j, $n, $chain, $generate);
                            }
                        }
                    }
                    if ($v_found) {
                        if ($initHash) {
                            return $chain;
                        }
                        if ($fulluri)
                            return igk_io_baseuri($chain) . $query;
                        return igk_io_currentrelativeuri($chain, $options) . $query;
                    }
                }
                $gs_uri = igk_html_get_system_uri($uri, $options);
                if ($gs_uri) {
                    $gs_uri = preg_replace("#(\.\./)+#", "_oth_/", $gs_uri);
                    $chain = igk_uri(IGK_RES_FOLDER . "/" . $gs_uri);
                    $o = igk_io_basedir($chain);
                    $outlink = null;
                    if (!file_exists($o) && IO::CreateDir(dirname($o))) {
                        if (!$createlink($uri, $o)) {
                            igk_debug_wln("failed to create link", $o);
                            igk_dev_wln_e("failed to create link", $o);
                        } else {
                            if (!is_link($o)) {
                                igk_debug_wln("link not create:" . $o);
                            }
                        }
                    }
                    if ($fulluri) {
                        $outlink = igk_io_baseuri($chain);
                    } else {
                        $outlink = igk_io_currentrelativeuri($chain, $options);
                    }
                    return $outlink . $query;
                }
            }
        }
        if ($appData === null) {
            $i = 1;
            if (!strstr(IGK_LIB_DIR, igk_uri(igk_io_applicationdir())))
                $i = 0;
            $appData = $i;
        }
        if (!$appData && (($pos = strpos(igk_uri($uri), IGK_LIB_DIR)) === 0)) {
            $v = ltrim(substr($uri, strlen(IGK_LIB_DIR)), "/");
            return igk_io_libdiruri($rp, $options) . $v . $query;
        }
        if (($v = igk_ajx_link($uri)) == null) {
            $v = igk_html_get_system_uri($uri, $options);
        }
        return $v . $query;
    }
    ///<summary>Represente resolveFullUri function</summary>
    ///<param name="uri"></param>
    public function resolveFullUri($uri)
    {
        $data = $this->resolve($uri);
        while (strpos($data, "../") === 0) {
            $data = substr($data, 3);
        }
        return igk_io_baseuri() . "/" . $data;
    }
    ///<summary>resolveOnly  file</summary>
    ///<param name="file"></param>
    ///<param name="notresolved" ref="true"></param>
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
            // $v_brpath = igk_io_baserelativepath($rp);
            foreach ($tab as $i => $j) {
                if (igk_io_is_subdir($i, $rp)) {
                    $s = $j;
                    $n = substr($rp, strlen($i) + 1);
                    if (is_object($s)) {
                        $b = $s->{'ini_chain'};
                        $chain = $b($n, $rp);
                    } else {
                        $chain = igk_uri(IGK_RES_FOLDER . "/" . $j . "/" . $n);
                       //$o = igk_io_basedir($chain);
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
                // $o = igk_io_basedir($chain);
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
